<?php

require_once '../config/database.php';
require_once '../models/Auth.php';

class AuthResource
{
    private $db;
    private $auth;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->auth = new Auth($this->db);
    }

    public function login()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->username) || empty($data->password)) {
            http_response_code(400);

            echo json_encode([
                "error" => "bad_request",
                "message" => "Username y password son obligatorios"
            ]);

            return;
        }

        $user = $this->auth->findUserByUsername($data->username);

        if (
            !$user ||
            $user["status"] !== "ACTIVE" ||
            !password_verify($data->password, $user["password_hash"])
        ) {
            http_response_code(401);

            echo json_encode([
                "error" => "invalid_credentials",
                "message" => "Usuario o contraseña incorrectos"
            ]);

            return;
        }

        $token = bin2hex(random_bytes(32));

        $expiresAt = date("Y-m-d H:i:s",
                           time() + 3600);

        $this->auth->revokeTokens($user["id"]);

           if ($this->auth->createToken(
               $user["id"],
               $token,
               $expiresAt))
        {
        http_response_code(200);

           echo json_encode([
                "access_token" => $token,
                "token_type" => "Bearer",
                "expires_at" => $expiresAt
               ]);

        return;
}

       http_response_code(500);

           echo json_encode([
                "error" => "token_creation_failed",
                "message" => "No se pudo generar el token"
               ]);
    }


    public function authenticate()
{
    header("Content-Type: application/json");

    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        http_response_code(401);

        echo json_encode([
            "error" => "unauthorized",
            "message" => "Token no proporcionado"
        ]);

        return false;
    }

    $authorization = $headers["Authorization"];

    if (!preg_match('/Bearer\s+(\S+)/', $authorization, $matches)) {
        http_response_code(401);

        echo json_encode([
            "error" => "unauthorized",
            "message" => "Formato de token inválido"
        ]);

        return false;
    }

    $token = $matches[1];

    $user = $this->auth->findValidToken($token);

    if (!$user) {
        http_response_code(401);

        echo json_encode([
            "error" => "unauthorized",
            "message" => "Token inválido, expirado o revocado"
        ]);

        return false;
    }

    return $user;
}


    public function me()
{
    header("Content-Type: application/json");

    $user = $this->authenticate();

    if (!$user) {
        return;
    }

    http_response_code(200);

    echo json_encode([
        "id" => $user["user_id"],
        "username" => $user["username"],
        "email" => $user["email"]
    ]);
}


    public function logout()
{
    header("Content-Type: application/json");

    $headers = getallheaders();

    if (!isset($headers["Authorization"])) {
        http_response_code(401);

        echo json_encode([
            "error" => "unauthorized",
            "message" => "Token no proporcionado"
        ]);

        return;
    }

    $authorization = $headers["Authorization"];

    if (!preg_match('/Bearer\s+(\S+)/', $authorization, $matches)) {
        http_response_code(401);

        echo json_encode([
            "error" => "unauthorized",
            "message" => "Formato de token inválido"
        ]);

        return;
    }

    $token = $matches[1];

    $user = $this->auth->findValidToken($token);

    if (!$user) {
        http_response_code(401);

        echo json_encode([
            "error" => "unauthorized",
            "message" => "Token inválido, expirado o revocado"
        ]);

        return;
    }

    $this->auth->revokeToken($token);

    http_response_code(200);

    echo json_encode([
        "message" => "Sesión cerrada correctamente"
    ]);
}


}
?>
