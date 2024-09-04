<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class FeatureRepository
{
    private PDO $pdo;
    
    public function __construct(private Database $database)
    {
        $this->pdo = $this->database->getConnection();
    }
    
    public function getAll(): array
    {
        $sql = "SELECT * FROM features";
        $stmt = $this->pdo->query($sql);
        
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        return $data;
    }
    
    public function create(array $data): string
    {
        $sql = "INSERT INTO features (description, created_at, updated_at) VALUES (:description, NOW(), NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":description", $data["description"], PDO::PARAM_STR);
        $stmt->execute();
        
        return $this->pdo->lastInsertId();
    }
    
    public function getById(int $id): array|bool
    {
        $sql = "SELECT * FROM features WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE features SET description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":description", $data["description"] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount();
    }


    public function featureExists(string $description): bool
    {
        $sql = "SELECT COUNT(*) FROM features WHERE description = :description";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":description", $description, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    public function delete(string $id): int
    {
        $sql = "DELETE FROM features WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount();
    }
}
