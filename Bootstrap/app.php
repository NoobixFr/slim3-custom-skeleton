<?php

use Respect\Validation\Validator as v;

session_start();

require __DIR__ .'/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [

        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,

        'db'       => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'slim',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ]
]);

$container = $app->getContainer();

// Init Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] =  function ($container) use ($capsule){
    return $capsule;
};

$container['auth'] = function ($container){
    return new \App\Auth\Auth();
};


$container['view'] = function($container){
    $view = new \Slim\Views\Twig(__DIR__ . '/../App/Resources/Views',[
        'cache' => false
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('auth', array(
        'check' => $container->auth->check(),
        'user' => $container->auth->user()
    ));

    return $view;
};

$container['validator'] = function($container){
    return new \App\Validation\Validator;
};

$container['AppController'] = function($container){
    return new \App\Controllers\AppController($container);
};

$container['AuthController'] = function($container){
    return new \App\Controllers\AuthController($container);
};

$container['csrf'] = function ($container){
    return new \Slim\Csrf\Guard;
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddeware($container));

$app->add($container->csrf);

v::with('App\\Validation\\Rules');

require __DIR__ . '/../App/routes.php';