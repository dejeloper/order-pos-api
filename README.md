<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Order POS API

Order POS API es una API backend desarrollada en Laravel para la gestión de puntos de venta (POS). Actualmente, el enfoque principal está en la administración de usuarios y productos, pero el objetivo es evolucionar hacia una solución integral que abarque todo el ciclo de ventas, cobranza, pagos, clientes, reportes, inventario, facturación y más.

## Descripción

Actualmente, el proyecto cuenta con la estructura básica de Laravel y funcionalidades iniciales para la gestión de usuarios y productos.  
A futuro, se planea implementar:

-   Gestión completa de clientes y pedidos
-   Cálculo y registro de pagos y recibos
-   Gestión y seguimiento de cobranza y llamadas
-   Generación de devoluciones
-   Listados y reportes diarios de llamadas y gestiones
-   Log de acciones y auditoría
-   Reportes y gráficas financieras
-   Ventas al contado, a cuotas y en POS
-   Control de inventario y productos
-   Gestión de nómina, cuentas por pagar/cobrar y facturación electrónica
-   Integración con sistemas externos y automatización de notificaciones

El objetivo es ofrecer una API robusta, escalable y adaptable a las necesidades de cualquier negocio de ventas.

Próximamente se irán sumando nuevas funcionalidades y módulos según el roadmap del proyecto.

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

## Contribuir

¡Las contribuciones son bienvenidas! Por favor, abre un issue o un pull request para sugerencias o mejoras.

## Licencia

Este proyecto está bajo la licencia MIT.
