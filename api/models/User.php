<?php

class User
{
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $name;
    public $email;
    public $password;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear usuario
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (nombre, email, password)
                  VALUES
                  (:name, :email, :password)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $passwordHash = password_hash(
            $this->password,
            PASSWORD_DEFAULT
        );

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $passwordHash);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Consultar todos los usuarios
    public function read()
    {
        $query = "SELECT
                    id,
                    nombre AS name,
                    email,
                    fecha_registro AS created_at
                  FROM " . $this->table_name . "
                  ORDER BY fecha_registro DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Consultar un usuario por ID
    public function readOne()
    {
        $query = "SELECT
                    id,
                    nombre AS name,
                    email,
                    fecha_registro AS created_at
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row["name"];
            $this->email = $row["email"];
            $this->created_at = $row["created_at"];

            return true;
        }

        return false;
    }

    // Actualizar usuario
    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET
                    nombre = :name,
                    email = :email
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Eliminar usuario
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>
