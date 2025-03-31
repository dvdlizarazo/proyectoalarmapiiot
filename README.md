# 📡 Proyecto de Monitoreo y Control con Raspberry Pi, Sensor de Movimiento y Sirena

## 📌 Introducción
Este proyecto tiene como objetivo desarrollar un **sistema de monitoreo y control** utilizando una **Raspberry Pi**, un **sensor de movimiento HC-SR501** y una **sirena HPS-103 de 12V**.

El sistema permite:
- Detectar movimiento y activar la sirena.
- Enviar notificaciones por **Telegram**.
- Controlar el sensor y la sirena de forma remota.
- Registrar eventos en un sistema web desarrollado en **Laravel 10**.

---
## 🛠️ **Componentes del Sistema**
### 🔌 Hardware
- **Raspberry Pi** (Modelo 3B, 4 o superior).
- **Sensor de movimiento HC-SR501**.
- **Sirena de 6 tonos HPS-103 (12V)**.
- **Fuente de alimentación adecuada**.

### 💻 Software
- **Laravel 10** (Backend y Dashboard de administración).
- **Python** (Gestión del sensor y sirena).
- **MySQL** (Base de datos para registros).
- **Telegram Bot API** (Notificaciones y control remoto).
- **Docker** (Opcional para despliegue).

---
## 🎯 **Funcionalidades del Proyecto**

### 📍 1. Detección de Movimiento
- El **sensor HC-SR501** detecta movimiento en su área de cobertura.
- Se activa automáticamente la **sirena de 6 tonos**.

### 🔔 2. Notificaciones por Telegram
- El script en **Python** envía alertas a Telegram.
- Desde Telegram, se puede **activar o desactivar** el sensor y la sirena.

### 📊 3. Registro en el Sistema
- Cada evento se **registra en la base de datos**.
- Un **dashboard en Laravel** permite visualizar en tiempo real el estado del sensor.

### 🔑 4. Control de Acceso
- Técnicos deben iniciar sesión en el portal web.
- Pueden **desactivar el sensor y la sirena** desde el dashboard.
- Al salir, el sistema **reactiva automáticamente** el sensor y la sirena.
- Se registra cada ingreso y salida.

---
## 🚀 **Instalación del Proyecto**

### 🔹 1️⃣ **Clonar el Repositorio**
```bash
git clone https://github.com/dvdlizarazo/proyectoalarmapiiot.git
cd tu_repositorio
```

### 🔹 2️⃣ **Instalar Dependencias**
```bash
composer install
npm install
```

### 🔹 3️⃣ **Configurar el Archivo `.env`**
```bash
cp .env.example .env
```
Edita el archivo `.env` y configura:
- **Base de datos:** `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- **Credenciales de Telegram Bot**

### 🔹 4️⃣ **Generar Clave de Aplicación**
```bash
php artisan key:generate
```

### 🔹 5️⃣ **Ejecutar Migraciones y Seeders**
```bash
php artisan migrate --seed
```

### 🔹 6️⃣ **Levantar el Servidor**
```bash
php artisan serve
```
El dashboard estará disponible en `http://127.0.0.1:8000`

---
## 📷 **Imágenes del Proyecto**
![Dashboard](assets/dashboard.png)

---
## 🏆 **Beneficios del Proyecto**
✅ **Seguridad Mejorada**: Detección de intrusos con alarmas inmediatas.  
✅ **Control Remoto**: Gestión total desde Telegram y el Dashboard.  
✅ **Historial de Actividades**: Registro detallado de eventos.  
✅ **Gestión de Acceso**: Control eficiente del ingreso de técnicos.  

---
## 📜 **Licencia**
Este proyecto está bajo la licencia [MIT](LICENSE).

---
## 👨‍💻 **Autor**
**Tu Nombre**  
📧 Contacto: [dvdlizarazo456@gmail.com](mailto:dvdlizarazo456@gmail.com)  
🐙 GitHub: [github.com/dvdlizarazo](https://github.com/dvdlizarazo)

