import RPi.GPIO as GPIO
import time

# Configuración de pines
pir_sensor = 11  # Pin del sensor PIR

# Configuración de GPIO
GPIO.setmode(GPIO.BOARD)
GPIO.setup(pir_sensor, GPIO.IN)  # Sensor PIR como entrada

# Función para verificar el sensor PIR
def check_motion():
    try:
        print("Esperando detección de movimiento...")
        while True:
            if GPIO.input(pir_sensor):  # Si se detecta movimiento
                print("¡Movimiento detectado!")
                time.sleep(1)  # Espera 1 segundo para evitar múltiples detecciones seguidas
            else:
                time.sleep(0.1)  # Reduce la carga de la CPU mientras espera
    except KeyboardInterrupt:
        print("Programa detenido por el usuario")
        GPIO.cleanup()  # Limpiar los pines al final

if __name__ == '__main__':
    check_motion()
