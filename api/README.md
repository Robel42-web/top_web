# API V3 - Gestión de Tareas

API REST desarrollada en PHP para la gestión de tareas, utilizando el enfoque **API-First** y una especificación **OpenAPI 3.0.3**.

## Descripción

La API permite crear, consultar y actualizar tareas.

Cada tarea contiene los siguientes datos:

- id
- titulo
- completada
- fecha_creacion

La versión V3 fue desarrollada utilizando el enfoque API-First, definiendo el contrato de la API mediante el archivo `openapi.yaml`.

## Tecnologías utilizadas

- PHP
- MariaDB
- REST
- JSON
- OpenAPI 3.0.3
- Swagger UI
- cURL

## Endpoints

### Obtener todas las tareas

```http
GET /api/v3/tareas
