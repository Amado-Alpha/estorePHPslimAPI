<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\CategoryRepository;
use Slim\Exception\HttpNotFoundException;

class GetCategory
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $category = $this->repository->getById((int) $id);
    
        if ($category === false) {
    
            throw new HttpNotFoundException($request,message: 'category not found');
    
        }

        $request = $request->withAttribute('category', $category);

        return $handler->handle($request);
    }
}