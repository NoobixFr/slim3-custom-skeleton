<?php
//App/Middleware/ValidationErrorsMidlleware.php

namespace App\Middleware;

/**
 * Permet de gérer les erreurs de validations dans notre application
 * Class ValidationErrorsMiddleware
 * @package App\Middleware
 */
class ValidationErrorsMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(isset($_SESSION['errors'])){
            $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
            unset($_SESSION['errors']);
        }


        $response = $next($request, $response);

        return $response;
    }
}