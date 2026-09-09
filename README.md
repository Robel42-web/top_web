# API REST - Productos, Autenticación y Gestión de Tareas

Proyecto desarrollado como una API REST utilizando **PHP, MySQL/MariaDB, OpenAPI y Swagger**.

El proyecto fue desarrollado de manera incremental mediante diferentes versiones de la API:

- **API REST de productos**
- **API V2:** autenticación mediante tokens.
- **API V3:** gestión de tareas utilizando el enfoque API-First.
- Documentación de la API V3 mediante **OpenAPI 3.0.3 y Swagger UI**.

---

## Tecnologías utilizadas

- PHP
- MySQL / MariaDB
- Apache
- PDO
- JSON
- REST
- OpenAPI 3.0.3
- Swagger UI
- Postman
- Git y GitHub

---

## Estructura principal del proyecto

```text
api/
├── config/
│   └── database.php
│
├── core/
│   └── Router.php
│
├── models/
│   ├── Auth.php
│   ├── Product.php
│   ├── User.php
│   └── Task.php
│
├── resources/
│   ├── v1/
│   ├── v2/
│   └── v3/
│       ├── ProductResource.php
│       ├── TaskResource.php
│       └── UserResource.php
│
├── public/
│   ├── api-docs/
│   │   └── index.html
│   ├── index.php
│   └── openapi.yaml
│
├── database.sql
├── openapi.yaml
└── README.md
```

---

# Base de datos

El proyecto utiliza una base de datos MySQL/MariaDB.

Entre las tablas utilizadas se encuentran:

```text
api_users
api_tokens
productos
tareas
usuarios
```

Para crear las tablas necesarias se incluye el archivo:

```text
database.sql
```

## Importar la base de datos

Primero ingresar a MySQL/MariaDB:

```bash
mysql -u root -p
```

Seleccionar la base de datos:

```sql
USE nombre_base_datos;
```

Ejecutar el archivo SQL:

```sql
SOURCE /ruta/del/proyecto/api/database.sql;
```

Por ejemplo, si el proyecto se encuentra en:

```text
/var/www/miapp/api
```

se puede ejecutar:

```sql
SOURCE /var/www/miapp/api/database.sql;
```

Finalmente se pueden comprobar las tablas:

```sql
SHOW TABLES;
```

---

# Configuración de la conexión

La configuración de la base de datos se encuentra en:

```text
config/database.php
```

Se deben configurar los datos correspondientes al servidor:

```php
$host = "localhost";
$db_name = "nombre_base_datos";
$username = "usuario";
$password = "contraseña";
```

Los valores deben modificarse de acuerdo con la configuración de cada equipo o servidor.

---

# Ejecución local

Entrar al directorio público de la API:

```bash
cd /var/www/miapp/api/public
```

Levantar el servidor integrado de PHP:

```bash
php -S localhost:8000
```

Si todo funciona correctamente, la API estará disponible mediante:

```text
http://localhost:8000
```

La API V3 estará disponible en:

```text
http://localhost:8000/api/v3
```

---

# API V3 - Gestión de tareas

La versión 3 implementa una API REST para gestionar tareas.

Cada tarea contiene los siguientes campos:

| Campo | Tipo | Descripción |
|---|---|---|
| id | Integer | Identificador de la tarea |
| titulo | String | Título de la tarea |
| completada | Boolean | Estado de la tarea |
| fecha_creacion | Timestamp | Fecha de creación |

## Endpoints

### Obtener todas las tareas

```http
GET /api/v3/tareas
```

Ejemplo:

```bash
curl http://localhost:8000/api/v3/tareas
```

---

### Crear una tarea

```http
POST /api/v3/tareas
```

Cuerpo JSON:

```json
{
  "titulo": "Aprender Swagger",
  "completada": false
}
```

Ejemplo con cURL:

```bash
curl -i -X POST http://localhost:8000/api/v3/tareas \
-H "Content-Type: application/json" \
-d '{
  "titulo": "Aprender Swagger",
  "completada": false
}'
```

Una creación correcta devuelve el código:

```text
201 Created
```

---

### Obtener una tarea por ID

```http
GET /api/v3/tareas/{id}
```

Ejemplo:

```bash
curl http://localhost:8000/api/v3/tareas/1
```

---

### Actualizar una tarea

```http
PUT /api/v3/tareas/{id}
```

Ejemplo:

```bash
curl -i -X PUT http://localhost:8000/api/v3/tareas/1 \
-H "Content-Type: application/json" \
-d '{
  "titulo": "Tarea actualizada",
  "completada": true
}'
```

Una actualización correcta devuelve:

```text
200 OK
```

---

# Documentación con OpenAPI

La especificación de la API V3 se encuentra en:

```text
openapi.yaml
```

El archivo utiliza:

```text
OpenAPI 3.0.3
```

La especificación contiene:

- Información general de la API.
- Servidores disponibles.
- Endpoints.
- Métodos HTTP.
- Parámetros.
- Cuerpos JSON.
- Códigos de respuesta.
- Esquema de las tareas.

---

# Swagger UI

El proyecto incluye una interfaz Swagger UI para consultar y probar los endpoints desde el navegador.

Con el servidor local iniciado:

```bash
php -S localhost:8000
```

abrir:

```text
http://localhost:8000/api-docs/
```

Swagger permite ejecutar directamente:

```text
GET  /tareas
POST /tareas
GET  /tareas/{id}
PUT  /tareas/{id}
```

mediante la opción **Try it out**.

---

# Pruebas con Postman

Los endpoints también pueden probarse utilizando Postman.

Por ejemplo:

```text
GET http://localhost:8000/api/v3/tareas
```

Para crear una tarea:

```text
POST http://localhost:8000/api/v3/tareas
```

Seleccionar:

```text
Body → raw → JSON
```

y enviar:

```json
{
  "titulo": "Tarea creada desde Postman",
  "completada": false
}
```

---

# Despliegue

La API también puede desplegarse en un servidor Apache.

En el servidor utilizado durante el desarrollo, el proyecto se encuentra dentro de:

```text
public_html/api
```

Después de actualizar el repositorio:

```bash
git pull
```

se debe comprobar la configuración de la base de datos e importar `database.sql` cuando sea necesario.

La documentación Swagger puede consultarse desde:

```text
/api/public/api-docs/
```

---

# Control de versiones

El proyecto utiliza Git para el control de versiones.

Entre las ramas desarrolladas se encuentran:

```text
main
api-rest-productos
api-v2-autenticacion
api-v3-api-first
```

La versión correspondiente a la implementación API-First y Swagger se encuentra en:

```text
api-v3-api-first
```

---

# API-First

La versión 3 utiliza el enfoque **API-First**, donde la estructura y comportamiento esperado de la API se documentan mediante una especificación OpenAPI.

Esto permite definir de forma clara:

- Rutas disponibles.
- Métodos HTTP.
- Datos de entrada.
- Datos de salida.
- Códigos de respuesta.
- Estructuras JSON.

La especificación puede visualizarse mediante Swagger, facilitando la documentación y las pruebas de la API.

---

## Evidencia: Swagger UI Funcionando
Vista principal:
<img width="1920" height="1080" alt="Captura de pantalla (455)" src="https://github.com/user-attachments/assets/106ea3c3-dbd6-4dd3-bd7c-831d91a9c737" />

Aquí podemos hacer cosas cencillas pero interesantes como un GET en tareas:
<img width="945" height="1080" alt="Captura de pantalla (445)" src="https://github.com/user-attachments/assets/ed315744-9a62-4385-98c2-e624aa068cc9" />
<img width="950" height="1080" alt="Captura de pantalla (446)" src="https://github.com/user-attachments/assets/e55acd86-1303-4390-b796-9f6fcd903107" />
 
O un POST como lo hicimos en la terminal y también en nuestra tabla tarea
<img width="936" height="1080" alt="Captura de pantalla (447)" src="https://github.com/user-attachments/assets/331f8b20-c858-48a5-a822-83bdb4e97447" />
<img width="963" height="1080" alt="Captura de pantalla (448)" src="https://github.com/user-attachments/assets/9462e228-b5f4-46d9-a407-b460a4665446" />

Un GET en nuestro ID, como lo hicimos anteriormente solo que con la diferencia de que en ves del ID 1 vamos a usar el 2 porque lo acabamos de crear:
<img width="942" height="1080" alt="Captura de pantalla (450)" src="https://github.com/user-attachments/assets/e26247d4-8ce6-4c70-96af-8d453c64044a" />
<img width="950" height="1080" alt="Captura de pantalla (451)" src="https://github.com/user-attachments/assets/d6289ace-19a0-4561-981a-553530c7c7b5" />
 
Y por ultimo y no menos importante un PUT.
<img width="941" height="1080" alt="Captura de pantalla (452)" src="https://github.com/user-attachments/assets/ffbd1179-bc12-49d6-ac2d-ce2698e9167d" />
<img width="961" height="1080" alt="Captura de pantalla (453)" src="https://github.com/user-attachments/assets/965b3c0f-f91f-4571-8d98-3b69fd549942" />

Y listo eso sería todo lo que la práctica nos pedía, aquí  les dejo la vista final.
 






