# ProStock Multi-tenant

Sistema de gestión de stock multi-tenant desarrollado con Laravel, que permite a múltiples empresas (tenants) gestionar su inventario de manera independiente.

## Características

- 🔐 Autenticación multi-tenant con Laravel Sanctum
- 🗄️ Base de datos independiente para cada tenant
- 👥 Gestión de usuarios y roles por tenant
- 🔄 Middleware para manejo automático de conexiones a bases de datos
- 🛠️ Comandos personalizados para gestión de tenants
- 🔒 Seguridad y aislamiento de datos entre tenants

## Requisitos

- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Laravel >= 10.0

## Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/arielarce2/prostock_multitenant.git
cd prostock_multitenant
```

2. Instalar dependencias:
```bash
composer install
```

3. Copiar el archivo de entorno:
```bash
cp .env.example .env
```

4. Generar la clave de la aplicación:
```bash
php artisan key:generate
```

5. Configurar la base de datos en el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prostock_central
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. Ejecutar las migraciones:
```bash
php artisan migrate
```

## Comandos Personalizados

### Limpiar Bases de Datos de Tenants
```bash
php artisan tenants:fresh
```
Este comando elimina todas las bases de datos de los tenants y ejecuta `migrate:fresh` en la base de datos central.

## Endpoints API

### Autenticación

#### Registro de Usuario
```http
POST /api/register
Content-Type: application/json

{
    "name": "Nombre Usuario",
    "email": "usuario@ejemplo.com",
    "password": "contraseña",
    "password_confirmation": "contraseña"
}
```

#### Inicio de Sesión
```http
POST /api/login
Content-Type: application/json

{
    "email": "usuario@ejemplo.com",
    "password": "contraseña"
}
```

#### Cierre de Sesión
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Obtener Perfil
```http
GET /api/profile
Authorization: Bearer {token}
```

## Estructura de Base de Datos

### Base de Datos Central
- Tabla `users`: Almacena información básica de usuarios y su base de datos asociada
- Tabla `personal_access_tokens`: Gestiona los tokens de autenticación

### Base de Datos por Tenant
- Tabla `users`: Usuarios específicos del tenant
- Tabla `roles`: Roles disponibles en el tenant
- Otras tablas específicas del negocio

## Middleware

### TenantMiddleware
Maneja automáticamente la conexión a la base de datos del tenant basado en el usuario autenticado.

## Seguridad

- Tokens de autenticación con Sanctum
- Aislamiento de datos entre tenants
- Validación de roles y permisos
- Protección contra CSRF
- Sanitización de datos de entrada

## Contribuir

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE.md](LICENSE.md) para más detalles.

## Contacto

Ariel Arce - arielarce2@gmail.com

Link del Proyecto: [https://github.com/arielarce2/prostock_multitenant](https://github.com/arielarce2/prostock_multitenant)
