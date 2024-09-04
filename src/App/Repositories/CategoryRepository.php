<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class CategoryRepository
{
    public function __construct(private Database $database)
    {
    }
    
    public function getAll(): array
    {
        $pdo = $this->database->getConnection();

        $stmt = $pdo->query('SELECT * FROM categories');
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Checking if category already exists
    public function categoryNameExists(string $name): bool 
    {
        $sql = "SELECT COUNT(*) FROM categories WHERE name = :name";
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Checking if category already exists
    public function categoryIdExists(int $id): bool 
    {
        $sql = "SELECT COUNT(*) FROM categories WHERE id = :id";
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }


    public function create(array $data): string
    {
        $sql = "INSERT INTO categories (name, created_at, updated_at)
                VALUES (:name, NOW(), NOW())";
                
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
    
        $stmt->execute();
        
        return $pdo->lastInsertId();
    }
    
    public function getById(int $id): array | bool
    {
        $sql = "SELECT *
                FROM categories
                WHERE id = :id";
                
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $data;
    }
    
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE categories
                SET name = :name,
                updated_at = NOW()
                WHERE id = :id";

        $pdo = $this->database->getConnection();       
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
    
    public function delete(string $id): int
    {
        $sql = "DELETE FROM categories
                WHERE id = :id";
                
        $pdo = $this->database->getConnection();        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->rowCount();
    }
}











