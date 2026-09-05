<?php

class Auth
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findUserByUsername($username)
    {
        $query = "SELECT
                    id,
                    username,
                    email,
                    password_hash,
                    status
                  FROM api_users
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function revokeTokens($userId)
{
    $query = "UPDATE api_tokens
              SET revoked = TRUE
              WHERE user_id = :user_id
              AND revoked = FALSE";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":user_id", $userId);

    return $stmt->execute();
}


    public function createToken($userId, $token, $expiresAt)
{
    $query = "INSERT INTO api_tokens
              (user_id, token, expires_at, revoked)
              VALUES
              (:user_id, :token, :expires_at, FALSE)";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":user_id", $userId);
    $stmt->bindParam(":token", $token);
    $stmt->bindParam(":expires_at", $expiresAt);

    return $stmt->execute();
}

    public function findValidToken($token)
{
    $query = "SELECT
                t.id,
                t.user_id,
                t.token,
                t.expires_at,
                t.revoked,
                u.username,
                u.email,
                u.status
              FROM api_tokens t
              INNER JOIN api_users u
                ON t.user_id = u.id
              WHERE t.token = :token
                AND t.revoked = FALSE
                AND t.expires_at > NOW()
                AND u.status = 'ACTIVE'
              LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":token", $token);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    public function revokeToken($token)
{
    $query = "UPDATE api_tokens
              SET revoked = TRUE
              WHERE token = :token";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":token", $token);

    return $stmt->execute();
}

  }
?>
