<?php
//App/Middleware/GuestMidlleware.php

namespace App\Middleware;

/**
 * Permet d'empecher l'accès à certaine page une fois connecté à l'application
 * Class GuestMiddleware
 * @package App\Middleware
 */
class GuestMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if($this->container->auth->check()){
            return $response->withRedirect($this->container->router->pathFor('homepage'));
        }


        $response = $next($request, $response);
        return $response;
    }
}