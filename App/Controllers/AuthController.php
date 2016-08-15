<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;
use App\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{

    public function getSignUp($request, $response){
        return $this->view->render($response, 'auth/signup.html.twig');
    }

    public function postSignUp($request, $response){

        $validation = $this->validator->validate($request, [
                'name'      => v::noWhitespace()->notEmpty()->alpha(),
                'email'     => v::noWhitespace()->notEmpty()->email()->EmailAvailable(),
                'password'  => v::noWhitespace()->notEmpty()
            ]);

        if($validation->failed()){
            return $response->withRedirect($this->router->pathFor("auth.signup"));
        }

        $user = User::create([
            "name" => $request->getParam('name'),
            "email" => $request->getParam('email'),
            "password" => password_hash($request->getParam('password'), PASSWORD_DEFAULT),

        ]);

        // On log l'utilisateur si on a reussi à le créer
        $this->auth->attempt(
            $user->email,
            $request->getParam('password')
        );

        return $response->withRedirect($this->router->pathFor("homepage"));
    }

    public function getSignIn($request, $response){
        return $this->view->render($response, 'auth/signin.html.twig');
    }

    public function postSignIn($request, $response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if(!$auth){
            return $response->withRedirect($this->router->pathFor("auth.signin"));
        }


        return $response->withRedirect($this->router->pathFor("homepage"));
    }

    public function getSignOut($request, $response)
    {
        $this->auth->logout();
        return $response->withRedirect($this->router->pathFor("homepage"));
    }

}