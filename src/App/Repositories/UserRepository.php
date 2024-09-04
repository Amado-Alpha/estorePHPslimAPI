<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(private Database $database)
    {
        $this->pdo = $this->database->getConnection();
    }

    public function getAll(): array
    {

        $stmt = $this->pdo->query('SELECT * FROM users');
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM users
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":username", $data["username"], PDO::PARAM_STR);
        $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindValue(":password", password_hash($data["password"], PASSWORD_DEFAULT), PDO::PARAM_STR);
        
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $sql = 'UPDATE users
        SET username = :username, 
            email = :email, 
            updated_at = NOW()';

        // Add password update only if it's provided
        if (!empty($data['password'])) {
            $sql .= ', password = :password';
        }

        $sql .= ' WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);

        // Bind password only if it's provided and hash it
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        }

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getByUsername(string $username): array | false
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail(string $email): array | false
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(string $id): int
    {
        $sql = 'DELETE FROM users
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}
