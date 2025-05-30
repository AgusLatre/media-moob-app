# Prueba tecnica Media Moob - Agustin Latrechiana

Este proyecto permite simular el envio de mensajes a múltiples plataformas (Telegram, WhatsApp, Slack, Discord).

## Herramientas utilizadas

- Laravel 11
- Laravel Breeze (por su simpleza y uso con Blade)
- Factory + Strategy Pattern para seleccionar servicio de mensajería
- Service Container para desacoplar la lógica
- Service Provider para registrar servicios
- Logging de eventos importantes
- API Restful sin autenticación para pruebas

## First Steps


1.  **Clonar el repositorio:**

    ```bash
    git clone https://github.com/AgusLatre/media-moob-app.git media-moob-app
    cd media-moob-app
    ```

2.  **Instalar dependencias de PHP:**

    ```bash
    composer install
    ```

3.  **Definir variables de entorno:**
    Copy the `.env.example` file to `.env` and configure your database connection and any other necessary environment variables.

    ```bash
    cp .env.example .env
    ```
    Then, open `.env` in your text editor and update the `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` variables as needed.

4.  **Gerar la key de la aplicación:**

    ```bash
    php artisan key:generate
    ```

5.  **Correr migraciones (en caso de imporar la bd actual, omitir):**

    ```bash
    php artisan migrate
    ```

6.  **Crear el symbolic link para los archivos multimedia :**

    ```bash
    php artisan storage:link
    ```

7.  **Serve la app (Laravel's built-in server):**

    ```bash
    php artisan serve
    ```
    This will typically start the application at `http://127.0.0.1:8000`.

8.  **Instalar dependencias de Node.js y compilar assets:**

    ```bash
    npm install
    npm run dev
    ```
    Or, for production-ready assets:
    ```bash
    npm run build
    ```

## Paquetes extras y docu usada
- https://www.chartjs.org/docs/latest/ (Para los diagramas de las metricas)
- https://stackoverflow.com/questions (Principal fuente para resolver cuestiones)
- Codeium (Refuerzo de el uso de PHPUnit)
- Postman (Prueba de la API)



## Inicio de sesion
Armé un command para poder crear un usuario que sea admin y asi puedan ver los mensajes de cualquier usario.
Si se corre esto, se ejecuta.
``` bash
php artisan create:admin-user
```
Pueden darle parametros a su gusto, sino, los actuales son "Nombre" | email | password.

## Vista Send
Formulario para enviar mensajes con validacion en frontend y en backend.

## Vista Sent
En caso de usuarios, van a ver solamente sus mensajes enviados.
En caso de usuarios administradores, van a ver los mensajes enviados de todos los usuarios, ademas de poder acceder a las metricas.

## Enviar mensajes

Desde la vista "Send", se puede seleccionar una plataforma, múltiples destinatarios y opcionalmente un archivo adjunto.

También se puede enviar por API con una estructura como:

```json
{
  "platform": "Slack",
  "recipients": ["user1", "user2"],
  "message": "Hola desde la API"
}
```

## Test
- test_message_can_be_stored	-> Que se pueda guardar correctamente un mensaje
- test_message_requires_platform	-> Que el campo platform sea obligatorio
- test_message_requires_valid_platform	-> Que sólo se acepten Telegram, Whatsapp, etc.
- test_message_requires_recipients	-> Que los destinatarios sean obligatorios
- test_authenticated_user_can_see_own_messages	-> Que sólo se vean los mensajes del usuario logueado
- test_attachment_is_optional	-> Que el mensaje se guarda sin archivo adjunto
