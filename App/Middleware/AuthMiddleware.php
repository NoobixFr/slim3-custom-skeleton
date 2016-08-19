<?php
//App/Middleware/AuthMidlleware.php
namespace App\Middleware;

/**
 * Permet de vérifier si un utilisateur est connecté
 * Class AuthMiddleware
 * @package App\Middleware
 */
class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(!$this->container->auth->check()){
            $this->container->flash->addMessage('danger', 'please sign in before do that');
            return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }


        $response = $next($request, $response);
        return $response;
    }
}