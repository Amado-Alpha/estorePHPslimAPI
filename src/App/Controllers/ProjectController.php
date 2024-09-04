<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\ProjectRepository;
use Valitron\Validator;
use App\Requests\CreateProjectRequest;
use App\Requests\EditProjectRequest;
use App\Database;
use PDO;

class ProjectController
{
    public function __construct(private ProjectRepository $repository, Database $database)
    {
        $this->repository = $repository;
        $this->database = $database;
    
    }


    public function index(Request $request, Response $response): Response
    {
        $projects = $this->repository->getAll();
        $response->getBody()->write(json_encode($projects));
        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $project = $request->getAttribute('project');

        $body = json_encode($project);
    
        $response->getBody()->write($body);
    
        return $response;        
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        
        $projectRequest = new CreateProjectRequest($body, $this->repository);

        if (!$projectRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($projectRequest->getErrors()));

            return $response->withStatus(422);
        }

        $id = $this->repository->create($body);

        $body = json_encode([
            'message' => 'Project created',
            'id' => $id
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }


    public function update(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();

        $projectRequest = new EditProjectRequest($body);

        if (!$projectRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($projectRequest->getErrors()));

            return $response->withStatus(422);
        }

        $rows = $this->repository->update((int) $id, $body);

        $body = json_encode([
            'message' => 'Project updated',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }


    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->repository->delete($id);

        $body = json_encode([
            'message' => 'Project deleted',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}