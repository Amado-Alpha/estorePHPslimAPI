<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\ProjectRepository;
use Slim\Exception\HttpNotFoundException;

class GetProject
{
    public function __construct(private ProjectRepository $repository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $project = $this->repository->getById((int) $id);
    
        if ($project === false) {
    
            throw new HttpNotFoundException($request,message: 'project not found');
    
        }

        $request = $request->withAttribute('project', $project);

        return $handler->handle($request);
    }
}