<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Order POS API

Order POS API es una API backend desarrollada en Laravel para la gestión de pedidos en puntos de venta (POS). El objetivo es ofrecer una API robusta y escalable para administrar órdenes, productos y usuarios en un entorno de ventas.

## Descripción

Actualmente, el proyecto cuenta con la estructura básica de Laravel y está en desarrollo la gestión de pedidos y productos. Próximamente se implementarán autenticación, control de inventario y reportes.

## Requisitos

-   PHP >= 8.1
-   Composer
-   MySQL o MariaDB
-   Extensiones PHP recomendadas por Laravel

## Instalación

1. **Clona el repositorio:**

    ```bash
    git clone https://github.com/dejeloper/order-pos-api.git
    cd order-pos-api
    ```

2. **Instala las dependencias de PHP:**

    ```bash
    composer install
    ```

3. **Copia el archivo de entorno y configura tus variables:**

    ```bash
    cp .env.example .env
    # Edita .env con tus credenciales de base de datos
    ```

4. **Genera la clave de la aplicación:**

    ```bash
    php artisan key:generate
    ```

5. **Ejecuta las migraciones:**

    ```bash
    php artisan migrate
    ```

6. **(Opcional) Si tienes seeders, ejecútalos:**

    ```bash
    php artisan db:seed
    ```

7. **Levanta el servidor de desarrollo:**
    ```bash
    php artisan serve
    ```

La API estará disponible en `http://localhost:8000`.

## Documentación de la API

Este proyecto utiliza Swagger para documentar los endpoints.  
Una vez instalado y configurado, puedes acceder a la documentación interactiva en:

```
http://localhost:8000/api/documentation
```

## Estructura actual

-   Gestión de pedidos (en desarrollo)
-   Gestión de productos (en desarrollo)
-   Gestión de usuarios y roles
-   Autenticación JWT (en desarrollo)
-   Estructura base de Laravel

## Contribuir

¡Las contribuciones son bienvenidas! Por favor, abre un issue o un pull request para sugerencias o mejoras.

## Licencia

Este proyecto está bajo la licencia MIT.
