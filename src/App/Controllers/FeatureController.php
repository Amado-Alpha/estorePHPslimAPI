<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\FeatureRepository;
use Valitron\Validator;
use App\Requests\CreateFeatureRequest;
use App\Requests\EditFeatureRequest;
use App\Database;
use PDO;

class FeatureController
{
    public function __construct(private FeatureRepository $repository, Database $database)
    {
        $this->repository = $repository;
        $this->database = $database;
    
    }


    public function index(Request $request, Response $response): Response
    {
        $features = $this->repository->getAll();
        $response->getBody()->write(json_encode($features));
        return $response;
    }

    public function show(Request $request, Response $response, string $id): Response
    {
        $feature = $request->getAttribute('feature');

        $body = json_encode($feature);
    
        $response->getBody()->write($body);
    
        return $response;        
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
    
        $featureRequest = new CreateFeatureRequest($body, $this->repository);

        if (!$featureRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($featureRequest->getErrors()));

            return $response->withStatus(422);
        }

        $id = $this->repository->create($body);

        $body = json_encode([
            'message' => 'Feature created',
            'id' => $id
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }


    public function update(Request $request, Response $response, string $id): Response
    {
        $body = $request->getParsedBody();

        $featureRequest = new EditFeatureRequest($body);

        if (!$featureRequest->validate()) {
            $response->getBody()
                     ->write(json_encode($featureRequest->getErrors()));

            return $response->withStatus(422);
        }

        $rows = $this->repository->update((int) $id, $body);

        $body = json_encode([
            'message' => 'Feature updated',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }


    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->repository->delete($id);

        $body = json_encode([
            'message' => 'Feature deleted',
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}