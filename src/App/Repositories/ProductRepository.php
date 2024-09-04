<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class ProductRepository
{
    
    public function __construct(private Database $database)
    {
        $this->pdo = $this->database->getConnection();
    }

    public function getAll(): array
    {
        // $pdo = $this->database->getConnection();

        $stmt = $this->pdo->query('SELECT * FROM products');
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM products
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string
    {
        $sql = 'INSERT INTO products (name, description, price, category_id, image_url, created_at, updated_at)
                VALUES (:name, :description, :price, :category_id, :image_url, NOW(), NOW())';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":description", $data["description"], PDO::PARAM_STR);
        $stmt->bindValue(":price", $data["price"], PDO::PARAM_STR);
        $stmt->bindValue(":category_id", $data["category_id"], PDO::PARAM_INT);
        $stmt->bindValue(":image_url", $data["image_url"], PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId();
    }


    public function update(int $id, array $data): int
    {
        $sql = 'UPDATE products
                SET name = :name, 
                description = :description,
                price = :price, 
                category_id = :category_id, 
                image_url = :image_url,
                updated_at = NOW()
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(":description", $data["description"], PDO::PARAM_STR);
        $stmt->bindValue(':price', $data['price'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $data['category_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':image_url', $data['image_url'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


    public function delete(string $id): int
    {
        $sql = 'DELETE FROM products
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}