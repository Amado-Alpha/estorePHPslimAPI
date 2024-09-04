<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use Valitron\Validator;
use App\Requests\CreateProductRequest;
use App\Requests\EditProductRequest;
use App\Database;
use PDO;

class ProductController
{
    public function __construct(private ProductRepository $repository, Database $database)
    {
        $this->repository = $repository;
        $this->database = $database;
    
    }


    public function index(Request $request, Response $response): Response
    {
        $products = $this->repository->getAll();
        $response->getBody()->write(json_encode($products));
        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $product = $request->getAttribute('product');

        $body = json_encode($product);
    
        $response->getBody()->write($body);
    
        return $response;        
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        
        $categoryRepository = new CategoryRepository($this->database);

        $productRequest = new CreateProductRequest($body, $categoryRepository);

        if (!$productRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($productRequest->getErrors()));

            return $response->withStatus(422);
        }

        $id = $this->repository->create($body);

        $body = json_encode([
            'message' => 'Product created',
            'id' => $id
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }


    public function update(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();

        $productRequest = new EditProductRequest($body);

        if (!$productRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($productRequest->getErrors()));

            return $response->withStatus(422);
        }

        $rows = $this->repository->update((int) $id, $body);

        $body = json_encode([
            'message' => 'Product updated',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }


    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->repository->delete($id);

        $body = json_encode([
            'message' => 'Product deleted',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}