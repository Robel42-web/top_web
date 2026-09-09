<?php

class Task
{
    private $conn;
    private $table_name = "tareas";

    public $id;
    public $titulo;
    public $completada;
    public $fecha_creacion;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear una tarea
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (titulo, completada)
                  VALUES
                  (:titulo, :completada)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":completada", $this->completada);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Consultar todas las tareas
    public function read()
    {
        $query = "SELECT
                    id,
                    titulo,
                    completada,
                    fecha_creacion
                  FROM " . $this->table_name . "
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Consultar una tarea
    public function readOne()
    {
        $query = "SELECT
                    id,
                    titulo,
                    completada,
                    fecha_creacion
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->titulo = $row["titulo"];
            $this->completada = $row["completada"];
            $this->fecha_creacion = $row["fecha_creacion"];

            return true;
        }

        return false;
    }

    // Actualizar una tarea
    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET
                    titulo = :titulo,
                    completada = :completada
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":completada", $this->completada);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
