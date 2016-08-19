<?php
//Bootstrap/app.php

use Respect\Validation\Validator as v;

session_start();

require __DIR__ .'/../vendor/autoload.php';

// Définition de la configuration de slim
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

// Récupération du container
$container = $app->getContainer();

// Initialisation de la base de données
$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Ajout de l'objet eloquent au container
$container['db'] =  function ($container) use ($capsule){
    return $capsule;
};

// Ajout de l'bjet de gestion de l'authentification au container.
$container['auth'] = function ($container){
    return new \App\Auth\Auth();
};

// Ajout de l'objet de gestion des messages flash au container
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// Ajout de l'objet de gestion des vues avec twig au container
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

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};

// Ajout de l'objet de validation des données au container
$container['validator'] = function($container){
    return new \App\Validation\Validator;
};

// Ajout des controllers de mon application au container
$container['AppController'] = function($container){
    return new \App\Controllers\AppController($container);
};

$container['AuthController'] = function($container){
    return new \App\Controllers\AuthController($container);
};

// Ajout de la protection contre les failles Csrf
$container['csrf'] = function ($container){
    return new \Slim\Csrf\Guard;
};

// Ajout des différents Middleware
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddeware($container));

// Ajout du MiddleWare pour toutes les routes.
$app->add($container->get('csrf'));

// Ajout des régles de validations pour valider les données
v::with('App\\Validation\\Rules');

// Appelle du fichier de routing.
require __DIR__ . '/../App/routes.php';