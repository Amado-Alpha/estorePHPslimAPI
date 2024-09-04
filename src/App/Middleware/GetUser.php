<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\UserRepository;
use Slim\Exception\HttpNotFoundException;

class GetUser
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $user = $this->repository->getById((int) $id);
    
        if ($user === false) {
    
            throw new HttpNotFoundException($request,message: 'user not found');
    
        }

        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}