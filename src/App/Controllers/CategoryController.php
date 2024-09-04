<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\CategoryRepository;
use Valitron\Validator;
use App\Requests\CreateCategoryRequest;
use App\Requests\EditCategoryRequest;


class CategoryController
{
    public function __construct(private CategoryRepository $repository)
    {
    }
    
    public function index(Request $request, Response $response): Response
    {
        $categories = $this->repository->getAll();
        $response->getBody()->write(json_encode($categories));
        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $category = $request->getAttribute('category');

        $body = json_encode($category);
    
        $response->getBody()->write($body);
    
        return $response;        
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        $categoryRequest = new CreateCategoryRequest($body, $this->repository);

        if (!$categoryRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($categoryRequest->getErrors()));

            return $response->withStatus(422);
        }

        $id = $this->repository->create($body);

        $body = json_encode([
            'message' => 'Category created',
            'id' => $id
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }


    public function update(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();

        $categoryRequest = new EditCategoryRequest($body);

        if (!$categoryRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($categoryRequest->getErrors()));

            return $response->withStatus(422);
        }

        $rows = $this->repository->update((int) $id, $body);

        $body = json_encode([
            'message' => 'Category updated',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }


    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->repository->delete($id);

        $body = json_encode([
            'message' => 'Category deleted',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }
    
}

