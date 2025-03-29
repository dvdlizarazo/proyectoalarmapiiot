import RPi.GPIO as GPIO
import time
import requests
from telegram import Update
from telegram.ext import Application, CommandHandler, ContextTypes
from flask import Flask, render_template, request, redirect, session
import threading
from datetime import datetime
import pytz  # Importar pytz para la zona horaria de Colombia
import os  # Importa os para generar un secret_key aleatorio

# Configuración de pines
pir_sensor = 11
piezo = 7
GPIO.setmode(GPIO.BOARD)
GPIO.setup(piezo, GPIO.OUT)
GPIO.setup(pir_sensor, GPIO.IN)

# Variables de control
sensor_activado = True  # Estado del sensor
entrada_time = None  # Hora de entrada
salida_time = None  # Hora de salida
current_user_id = None  # Variable global para almacenar el user_id

# Zona horaria de Colombia
colombia_tz = pytz.timezone('America/Bogota')

# Token y chat ID del bot de notificaciones
NOTIFICATION_BOT_TOKEN = "7894171604:AAEgpWtic7HtqhxzgggXbAdFSqm04OMwkeU"
CHAT_IDS = ["5523477122", "2064371910", "7065641121"]  # Reemplaza con el chat ID donde quieres enviar la notificación

 
# Flask App para el portal de acceso
app = Flask(__name__)
app.secret_key = os.urandom(24)
app.config['SESSION_TYPE'] = 'filesystem'  # Configuración para almacenar sesiones
# Configura Flask para aceptar cookies de sesión correctamente
app.config['SESSION_COOKIE_HTTPONLY'] = True
app.config['SESSION_COOKIE_SECURE'] = False  # Cambia a True si usas HTTPS

detected_at = datetime.now(pytz.timezone('America/Bogota')).strftime('%Y-%m-%d %H:%M:%S')


# Función para enviar notificación a múltiples chat_ids
def send_notification(message):
    url = f"https://api.telegram.org/bot{NOTIFICATION_BOT_TOKEN}/sendMessage"
    for chat_id in CHAT_IDS:
        payload = {
            "chat_id": chat_id,
            "text": message
        }
        # Enviar la solicitud a Telegram
        requests.post(url, data=payload)

# Función para autenticar al usuario con correo y contraseña en Laravel
def login_with_laravel(email, password):
    url = "https://proyecto.alarmapiiot.cloud/api/login"  # URL de tu API de Laravel
    data = {
        'email': email,
        'password': password
    }

    # Realizar la solicitud POST al endpoint de login
    response = requests.post(url, data=data)

    if response.status_code == 200:
        # Si la respuesta es exitosa, devolver el token y los datos del usuario
        return response.json()['token'], response.json()['user']
    else:
        # Si las credenciales son incorrectas o ocurre algún otro error
        return None, None

# Función para enviar la entrada o salida a la API Laravel
def send_status_to_server(tipo, api_url, sensor_name=None, status=None, detected_at=None):
    # Obtener la fecha actual en la zona horaria de Colombia
    fecha_registro = datetime.now(pytz.timezone('America/Bogota')).strftime('%Y-%m-%d %I:%M %p')

    # Convertir fecha al formato correcto 'Y-m-d H:i:s'
    fecha_registro_formateada = datetime.strptime(fecha_registro, '%Y-%m-%d %I:%M %p').strftime('%Y-%m-%d %H:%M:%S')
    
    payload = {
        "tipo": tipo,
        "id_usuario": current_user_id,  # Usamos la variable global con el id del usuario
        "id_ubicacion": 1,  # Puedes modificar este valor si es necesario
        "fecha_registro": fecha_registro_formateada
    }

    # Agregar datos del sensor si están presentes
    if sensor_name and status is not None and detected_at:
        payload['sensor_name'] = sensor_name
        payload['status'] = status
        payload['detected_at'] = detected_at

    try:
        # Enviar la solicitud POST a la API con la URL específica
        response = requests.post(api_url, json=payload, headers={"Content-Type": "application/json", "Accept": "application/json"})

        # Verificar la respuesta
        if response.status_code == 201:
            print(f"Datos registrados correctamente en la API {api_url}.")
        else:
            print(f"Error al enviar al servidor {api_url}: {response.status_code} - {response.text}")
            print(response.json())
    except Exception as e:
        print(f"Error de conexión con la API {api_url}: {e}")

# Función para manejar el sensor de movimiento
def sensor_loop():
    global sensor_activado
    try:
        while True:
            if sensor_activado:
                current_state = GPIO.input(pir_sensor)
                print(current_state)
                if current_state == 1:
                    print(f"Movimiento detectado alarma pi {pir_sensor}")
                    GPIO.output(piezo, True)
                    
                    # Enviar notificación a Telegram
                    send_notification(f"Movimiento detectado alarma pi {pir_sensor}")
                    
                    # Enviar estado al servidor de sensores de movimiento
                    send_status_to_server(
                        tipo="movimiento",  # Puedes agregar un tipo si es necesario
                        api_url="https://proyecto.alarmapiiot.cloud/api/motion-sensors",
                        sensor_name=f"Sensor {pir_sensor}", 
                        status=True, 
                        detected_at=detected_at
                    )

                    time.sleep(1)  # Sonido de la sirena
                    GPIO.output(piezo, False)
                    time.sleep(5)  # Evitar lecturas constantes
            else:
                time.sleep(1)  # Pausa mientras el sensor está desactivado
    except KeyboardInterrupt:
        pass
    finally:
        GPIO.cleanup()

# Rutas para el portal web de control de acceso
@app.route('/', methods=['GET', 'POST'])
def index():
    global entrada_time, salida_time, sensor_activado

    try:
        if request.method == 'POST':
            email = request.form['email']
            password = request.form['password']

            # Limpiar valores de sesión anteriores si los hay
            session.clear()  # Limpia toda la sesión

            # Intentar hacer login con las credenciales de Laravel
            token, usuario = login_with_laravel(email, password)

            if token:
                # Almacenar el token y el usuario autenticado en la sesión
                session['token'] = token
                session['user'] = usuario  # Guardar toda la información del usuario
                session['user_id'] = usuario['id']  # Almacenar el id del usuario en la sesión
                global current_user_id
                current_user_id = usuario['id']  # Asignar el user_id global
                print(f"Token recibido y almacenado en sesión: {session}")  # Verifica la sesión aquí

                # Establecer la hora de entrada
                entrada_time = datetime.now(colombia_tz).strftime('%Y-%m-%d %I:%M %p')
                print(f"Hora de entrada registrada: {entrada_time}")  # Depuración

                # Enviar notificación a Telegram
                send_notification(f"Registro de entrada {entrada_time}")

                # Redirigir a la página de entrada
                return redirect('/entrada')
            else:
                print("Credenciales incorrectas")  # Depuración
                return "Credenciales incorrectas. Intenta nuevamente."
    except Exception as e:
        print(f"Error en la ruta index: {e}")
        return "Ha ocurrido un error al procesar la solicitud."

    return render_template('index.html')

@app.route('/entrada', methods=['GET'])
def entrada():
    global entrada_time
    global sensor_activado
    sensor_activado = False
    
    try:
        # Verificar si el token está presente en la sesión
        if 'token' not in session:
            print("No se encontró el token en la sesión. Redirigiendo a login...")
            return redirect('/')  # Si no hay token, redirige a la página de login

        print("Token encontrado en la sesión, accediendo a la página de entrada.")  # Depuración

        if entrada_time:
            # Enviar la entrada a la API Laravel con el id_usuario desde la sesión
            send_status_to_server(
                tipo="entrada", 
                api_url="https://proyecto.alarmapiiot.cloud/api/entrada-salida"
            )
                
            # Enviar estado "Inactivo" al servidor
            send_status_to_server(
                tipo="movimiento",  # Puedes agregar un tipo si es necesario
                sensor_name=f"Sensor {pir_sensor}", 
                status=False, 
                detected_at=detected_at,
                api_url="https://proyecto.alarmapiiot.cloud/api/motion-sensors"
            )
                
            return render_template('entrada.html', entrada_time=entrada_time)
        else:
            print("No se ha registrado hora de entrada. Redirigiendo a inicio...")
            return redirect('/')  # Si no se ha registrado entrada, redirige al inicio
    except Exception as e:
        print(f"Error en la ruta entrada: {e}")
        return "Ha ocurrido un error en la entrada."



# Función de la ruta /salida
@app.route('/salida', methods=['GET', 'POST'])
def salida():
    global salida_time, entrada_time, sensor_activado
    
    try:
        if request.method == 'POST':  # Cuando el usuario marca la salida
            if entrada_time:  # Verifica si la hora de entrada está registrada
                salida_time = datetime.now(colombia_tz).strftime('%Y-%m-%d %I:%M %p')  # Formato 12 horas con zona horaria
                
                # Convertir las fechas de entrada y salida a objetos datetime
                entrada_dt = datetime.strptime(entrada_time, '%Y-%m-%d %I:%M %p')
                salida_dt = datetime.strptime(salida_time, '%Y-%m-%d %I:%M %p')
                
                # Calcular la diferencia en tiempo
                tiempo_trabajado = salida_dt - entrada_dt
                
                # Calcular las horas y minutos trabajados
                horas_trabajadas = tiempo_trabajado.total_seconds() / 3600  # en horas (puede ser decimal)
                horas_enteras = int(horas_trabajadas)  # Número de horas enteras
                minutos_trabajados = (horas_trabajadas - horas_enteras) * 60  # Obtener los minutos restantes
                minutos_trabajados = round(minutos_trabajados)  # Redondear a minutos más cercanos

                # Almacenar el resultado en una variable
                horas_formateadas = f"{horas_enteras} horas {minutos_trabajados} minutos"
                
                # Enviar notificación a Telegram
                send_notification(f"Registro de salida {salida_dt} - total horas trabajadas: {horas_formateadas} ")

                print(f"El técnico trabajó {horas_formateadas}.")
                
                # Enviar la salida a la API Laravel
                send_status_to_server(
                    tipo="salida", 
                    api_url="https://proyecto.alarmapiiot.cloud/api/entrada-salida"
                )
                
                # Enviar estado "activo" al servidor
                send_status_to_server(
                    tipo="movimiento",  # Puedes agregar un tipo si es necesario
                    sensor_name=f"Sensor {pir_sensor}", 
                    status=True, 
                    detected_at=detected_at,  # Formato de 24 horas
                    api_url="https://proyecto.alarmapiiot.cloud/api/motion-sensors"
                )

                # Activar el sensor al salir
                sensor_activado = True

                # Retornar la plantilla de salida con horas trabajadas
                return render_template('salida.html', salida_time=salida_time, horas_trabajadas=horas_formateadas)
            else:
                # Si no se registró la entrada, mostrar un mensaje en la misma página
                error_message = "Primero debe registrar la hora de entrada antes de marcar la salida."
                return render_template('salida.html', error_message=error_message)
        
        # Si es un GET, simplemente mostrar la página de salida
        return render_template('salida.html')

    except Exception as e:
        print(f"Error en la ruta salida: {e}")
        return f"Ha ocurrido un error al procesar la salida: {e}"


# Funciones de comandos de Telegram
async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    await update.message.reply_text("¡Hola! Usa /activar para activar el sensor y /desactivar para desactivarlo.")

async def activar(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    global sensor_activado
    sensor_activado = True
    await update.message.reply_text("El sensor de movimiento ha sido ACTIVADO.")
    
    # Enviar estado "activo" al servidor
    send_status_to_server(
        tipo="movimiento",
        sensor_name=f"Sensor {pir_sensor}", 
        status=True, 
        detected_at=detected_at,
        api_url="https://proyecto.alarmapiiot.cloud/api/motion-sensors"
    )
    print("Estado enviado al servidor: ACTIVADO")

async def desactivar(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    global sensor_activado
    sensor_activado = False
    await update.message.reply_text("El sensor de movimiento ha sido DESACTIVADO.")
    
    # Enviar estado "inactivo" al servidor
    send_status_to_server(
        tipo="movimiento",  # Puedes agregar un tipo si es necesario
        sensor_name=f"Sensor {pir_sensor}", 
        status=False, 
        detected_at=detected_at,
        api_url="https://proyecto.alarmapiiot.cloud/api/motion-sensors"
    )
    print("Estado enviado al servidor: DESACTIVADO")

# Configuración del bot
def start_bot():
    # Token de tu bot de Telegram
    TOKEN = "8092353788:AAEfCK9ELiFdNAT3bW9tVeGotWY8tHZTA8c"

    # Crear la aplicación
    app_tg = Application.builder().token(TOKEN).build()

    # Añadir manejadores de comandos
    app_tg.add_handler(CommandHandler("start", start))
    app_tg.add_handler(CommandHandler("activar", activar))
    app_tg.add_handler(CommandHandler("desactivar", desactivar))

    # Ejecutar el loop del sensor en un hilo separado
    sensor_thread = threading.Thread(target=sensor_loop, daemon=True)
    sensor_thread.start()

    # Iniciar el bot
    print("Bot de Telegram iniciado. Esperando comandos...")
    app_tg.run_polling()

# Iniciar Flask y Telegram en hilos separados
def main():
    # Iniciar el servidor web de Flask en un hilo separado
    flask_thread = threading.Thread(target=lambda: app.run(host='0.0.0.0', port=5000, use_reloader=False, debug=True))
    flask_thread.start()

    # Iniciar el bot de Telegram
    start_bot()

if __name__ == '__main__':
    main()
