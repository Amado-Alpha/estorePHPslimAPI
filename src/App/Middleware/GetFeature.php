<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\FeatureRepository;
use Slim\Exception\HttpNotFoundException;

class GetFeature
{
    public function __construct(private FeatureRepository $repository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $feature = $this->repository->getById((int) $id);
    
        if ($feature === false) {
    
            throw new HttpNotFoundException($request,message: 'feature not found');
    
        }

        $request = $request->withAttribute('feature', $feature);

        return $handler->handle($request);
    }
}