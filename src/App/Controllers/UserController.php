<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;
use Valitron\Validator;
use App\Requests\CreateUserRequest;
use App\Requests\EditUserRequest;
use App\Database;
use PDO;

class UserController
{
    public function __construct(private UserRepository $repository, Database $database)
    {
        $this->repository = $repository;
        $this->database = $database;
    
    }


    public function index(Request $request, Response $response): Response
    {
        $users = $this->repository->getAll();
        $response->getBody()->write(json_encode($users));
        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $user = $request->getAttribute('user');

        $body = json_encode($user);
    
        $response->getBody()->write($body);
    
        return $response;        
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
    
        $userRequest = new CreateUserRequest($body, $this->repository);

        if (!$userRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($userRequest->getErrors()));

            return $response->withStatus(422);
        }

        $id = $this->repository->create($body);

        $body = json_encode([
            'message' => 'User registered',
            'id' => $id
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }


    public function update(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();

        $userRequest = new EditUserRequest($body);

        if (!$userRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($userRequest->getErrors()));

            return $response->withStatus(422);
        }

        $rows = $this->repository->update((int) $id, $body);

        $body = json_encode([
            'message' => 'User updated',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }


    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->repository->delete($id);

        $body = json_encode([
            'message' => 'User deleted',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}