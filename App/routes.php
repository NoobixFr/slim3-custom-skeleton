<?php
//App/routes.php

// Ajout des middlewares pour sécuriser les routes
use \App\Middleware\AuthMiddleware;
use \App\Middleware\GuestMiddleware;

// Route accessible par tout le monde
$app->get('/', 'AppController:homepage')->setName('homepage');

// Route accessible en invité
$app->group('' , function(){

    $this->get('/auth/enregistrement', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/enregistrement', 'AuthController:postSignUp');

    $this->get('/auth/connexion', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/connexion', 'AuthController:postSignIn');

})->add(new GuestMiddleware($container));

// Route accessible une fois connecté.
$app->group('' , function(){

    $this->get('/auth/deconnexion', 'AuthController:getSignOut')->setName('auth.signout');

    $this->get('/auth/password/change', 'AuthController:getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', 'AuthController:postChangePassword');

})->add(new AuthMiddleware($container));