<?php
session_start();

require __DIR__ .'/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [

        'displayErrorDetails' => true,

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

$container['view'] = function($container){
    $view = new \Slim\Views\Twig(__DIR__.'/../app/resources/views',[
        'cache' => false
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    return $view;
};

$container['AppController'] = function($container){
    return new \App\Controllers\AppController($container);
};

require __DIR__ .'/../app/routes.php';