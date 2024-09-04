<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Middleware\AddJsonResponseHeader;
use App\Controllers\ProductIndex;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Controllers\ProjectController;
use App\Controllers\FeatureController;
use App\Controllers\UserController;
use App\Middleware\GetProduct;
use App\Middleware\GetFeature;
use App\Middleware\GetProject;
use App\Middleware\GetCategory;
use App\Middleware\GetUser;
use Slim\Routing\RouteCollectorProxy;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

$dotenv->load();

$builder = new ContainerBuilder;

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')
                     ->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);

$error_handler = $error_middleware->getDefaultErrorHandler();

$error_handler->forceContentType('application/json');

$app->add(new AddJsonResponseHeader);

$app->group('/api', function (RouteCollectorProxy $group) {


    // Products
    // $group->get('/products', ProductIndex::class);

    $group->post('/products', [ProductController::class, 'create']);

    $group->get('/products', [ProductController::class, 'index']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/products/{id:[0-9]+}', ProductController::class . ':show');

        $group->patch('/products/{id:[0-9]+}', ProductController::class . ':update');

        $group->delete('/products/{id:[0-9]+}', ProductController::class . ':delete');

    })->add(GetProduct::class);


    // Categories
    $group->post('/categories', [CategoryController::class, 'create']);

    $group->get('/categories', [CategoryController::class, 'index']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/categories/{id:[0-9]+}', CategoryController::class . ':show');

        $group->patch('/categories/{id:[0-9]+}', CategoryController::class . ':update');

        $group->delete('/categories/{id:[0-9]+}', CategoryController::class . ':delete');

    })->add(GetCategory::class);


    // Projects
    $group->post('/projects', [ProjectController::class, 'create']);

    $group->get('/projects', [ProjectController::class, 'index']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/projects/{id:[0-9]+}', ProjectController::class . ':show');

        $group->patch('/projects/{id:[0-9]+}', ProjectController::class . ':update');

        $group->delete('/projects/{id:[0-9]+}', ProjectController::class . ':delete');

    })->add(GetProject::class);

    // Features
    $group->post('/features', [FeatureController::class, 'create']);

    $group->get('/features', [FeatureController::class, 'index']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/features/{id:[0-9]+}', FeatureController::class . ':show');

        $group->patch('/features/{id:[0-9]+}', FeatureController::class . ':update');

        $group->delete('/features/{id:[0-9]+}', FeatureController::class . ':delete');

    })->add(GetFeature::class);

    // Users
    $group->post('/users', [UserController::class, 'create']);

    $group->get('/users', [UserController::class, 'index']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/users/{id:[0-9]+}', UserController::class . ':show');

        $group->patch('/users/{id:[0-9]+}', UserController::class . ':update');

        $group->delete('/users/{id:[0-9]+}', UserController::class . ':delete');

    })->add(GetUser::class);


});

$app->run();
