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
                'email'     => v::noWhitespace()->notEmpty()->email(),
                'password'  => v::noWhitespace()->notEmpty()
            ]);

        if($validation->failed()){
            return $response->withRedirect($this->router->pathFor("auth.signup"));
        }

        User::create([
            "name" => $request->getParam('name'),
            "email" => $request->getParam('email'),
            "password" => password_hash($request->getParam('password'), PASSWORD_DEFAULT),

        ]);

        return $response->withRedirect($this->router->pathFor("homepage"));
    }

}