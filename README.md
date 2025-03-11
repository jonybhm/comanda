- [Sobre / About](#sobre--about)
- [Características / Features](#características--features)
- [Hecho con / Built With](#hecho-con--built-with)
- [Empezando / Getting Started](#empezando--getting-started)
  - [Requisitos previos / Prerequisites](#requisitos-previos--prerequisites)
  - [Instalación / Installation](#instalación--installation)
- [Utilización / Usage](#utilización--usage)
- [Contribuciones / Contributing](#contribuciones--contributing)
- [Autores y contribuyentes / Authors & contributors](#autores-y-contribuyentes--authors--contributors)
- [Licencia / License](#licencia--license)
- [Reconocimientos / Acknowledgments](#reconocimientos--acknowledgments)

---

## Sobre / About

> Esta API REST permite gestionar los pedidos de un restaurante mediante un sistema basado en Slim 4. Se pueden administrar usuarios, productos, mesas y pedidos, con funcionalidades avanzadas como estadísticas, manejo de estados y generación de reportes.

---

## Características / Features

✅ **Administración de entidades:** ABM de usuarios, productos, mesas y pedidos. ✅ **Middleware de autenticación y permisos:** Verificación de usuarios para acceso a funcionalidades específicas. ✅ **Gestión de estados:** Control del estado de pedidos y empleados. ✅ **Carga y descarga de datos:** Soporte para archivos CSV y generación de reportes en PDF. ✅ **Seguimiento de acciones:** Registro de operaciones realizadas por los empleados. ✅ **Estadísticas:** Reportes detallados sobre pedidos y operaciones en los últimos 30 días.

---

## Hecho con / Built With

- **Slim Framework v4** - Microframework PHP para desarrollar la API.
- **PDO (PHP Data Objects)** - Manejo de la base de datos.
- **JSON Web Tokens (JWT)** - Autenticación segura.

---

## Empezando / Getting Started

### Requisitos previos / Prerequisites

- PHP 7.4 o superior
- Composer
- Servidor web (Apache o Nginx con soporte para PHP)
- Base de datos MySQL o PostgreSQL

### Instalación / Installation

1. Clonar el repositorio:

   ```sh
   git clone https://github.com/jonybhm/comanda.git
   ```

2. Instalar las dependencias:

   ```sh
   composer install
   ```

3. Configurar el archivo `.env` con los datos de la base de datos.

4. Ejecutar las migraciones de la base de datos:

   ```sh
   php migrations.php
   ```

5. Iniciar el servidor local:

   ```sh
   php -S localhost:8080 -t public
   ```

---

## Utilización / Usage

La API expone los siguientes endpoints:

- **Usuarios**
  - `POST /usuarios` - Crear un usuario
  - `GET /usuarios` - Listar usuarios
- **Productos**
  - `POST /productos` - Agregar un producto
  - `GET /productos` - Listar productos
- **Mesas**
  - `POST /mesas` - Crear una mesa
  - `GET /mesas` - Listar mesas
- **Pedidos**
  - `POST /pedidos` - Crear un pedido
  - `GET /pedidos` - Listar pedidos
  - `PUT /pedidos/{id}` - Actualizar estado de un pedido
- **Reportes**
  - `GET /reportes/empleados` - Ver estadísticas de empleados
  - `GET /reportes/pedidos` - Ver estadísticas de pedidos

---

## Contribuciones / Contributing

Si deseas contribuir, por favor revisa las [pautas de contribución](docs/CONTRIBUTING.md). Cualquier tipo de aporte es **bienvenido**.

---

## Autores y contribuyentes / Authors & contributors

El desarrollo de esta API fue realizado por [Jonathan De Castro](https://github.com/jonybhm).

Lista de [contribuyentes](https://github.com/jonybhm/REPO_SLUG/contributors).

---

## Licencia / License

Este proyecto está autorizado bajo la **Licencia MIT**.

Consulta [LICENCIA](LICENSE) para obtener más información.

---

## Reconocimientos / Acknowledgments

Agradecimientos a los docentes de la Universidad Teconológica Nacional FRA.

