<?php
//App/Middleware/OldinputhMidlleware.php

namespace App\Middleware;

/**
 * Permet re-remplir les champs de formulaires en cas d'erreur.
 * Class OldInputMiddleware
 * @package App\Middleware
 */
class OldInputMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(isset($_SESSION['old'])){
            $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
            unset($_SESSION['old']);
        }

        $_SESSION['old'] = $request->getParams();

        $response = $next($request, $response);

        return $response;
    }
}