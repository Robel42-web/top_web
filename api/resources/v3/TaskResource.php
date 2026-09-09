<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Task.php';

class TaskResource
{
    private $db;
    private $task;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();

        $this->task = new Task($this->db);
    }

    // GET /tareas
    public function index()
    {
        header("Content-Type: application/json");

        $stmt = $this->task->read();
        $tareas = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tareas[] = [
                "id" => (int)$row["id"],
                "titulo" => $row["titulo"],
                "completada" => (bool)$row["completada"],
                "fecha_creacion" => $row["fecha_creacion"]
            ];
        }

        http_response_code(200);

        echo json_encode([
            "data" => $tareas
        ]);
    }

    // GET /tareas/{id}
    public function show($id)
    {
        header("Content-Type: application/json");

        $this->task->id = $id;

        if ($this->task->readOne()) {
            http_response_code(200);

            echo json_encode([
                "id" => (int)$this->task->id,
                "titulo" => $this->task->titulo,
                "completada" => (bool)$this->task->completada,
                "fecha_creacion" => $this->task->fecha_creacion
            ]);

            return;
        }

        http_response_code(404);

        echo json_encode([
            "message" => "Tarea no encontrada"
        ]);
    }

    // POST /tareas
    public function store()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data["titulo"]) ||
            !isset($data["completada"])
        ) {
            http_response_code(400);

            echo json_encode([
                "message" => "Los campos titulo y completada son obligatorios"
            ]);

            return;
        }

        $this->task->titulo = $data["titulo"];
        $this->task->completada = $data["completada"] ? 1 : 0;

        if ($this->task->create()) {
            http_response_code(201);

            echo json_encode([
                "message" => "Tarea creada correctamente",
                "id" => (int)$this->task->id
            ]);

            return;
        }

        http_response_code(500);

        echo json_encode([
            "message" => "No se pudo crear la tarea"
        ]);
    }

    // PUT /tareas/{id}
    public function update($id)
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !isset($data["titulo"]) ||
            !isset($data["completada"])
        ) {
            http_response_code(400);

            echo json_encode([
                "message" => "Los campos titulo y completada son obligatorios"
            ]);

            return;
        }

        $this->task->id = $id;

        if (!$this->task->readOne()) {
            http_response_code(404);

            echo json_encode([
                "message" => "Tarea no encontrada"
            ]);

            return;
        }

        $this->task->titulo = $data["titulo"];
        $this->task->completada = $data["completada"] ? 1 : 0;

        if ($this->task->update()) {
            http_response_code(200);

            echo json_encode([
                "message" => "Tarea actualizada correctamente"
            ]);

            return;
        }

        http_response_code(500);

        echo json_encode([
            "message" => "No se pudo actualizar la tarea"
        ]);
    }
}
