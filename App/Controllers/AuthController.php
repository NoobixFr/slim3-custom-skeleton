<?php
// App/Controllers/AuthController.php
namespace App\Controllers;

use Respect\Validation\Validator as v;
use App\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    /*
     * Affiche le formulaire d'inscription
     */
    public function getSignUp($request, $response){
        return $this->view->render($response, 'auth/signup.html.twig');
    }

    /*
     * Vérifie le formulaire d'inscription et connecte l'utilisateur
     */
    public function postSignUp($request, $response){

        $validation = $this->validator->validate($request, [
                'name'      => v::noWhitespace()->notEmpty()->alpha(),
                'email'     => v::noWhitespace()->notEmpty()->email()->EmailAvailable(),
                'password'  => v::noWhitespace()->notEmpty()
            ]);

        if($validation->failed()){
            $this->flash->addMessage('danger', 'You can\'t signup');
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

        $this->flash->addMessage('info', 'You\'re now registered and logged');

        return $response->withRedirect($this->router->pathFor("homepage"));
    }

    /*
     * Affiche le formulaire de connexion
     */
    public function getSignIn($request, $response){
        return $this->view->render($response, 'auth/signin.html.twig');
    }

    /*
     * Vérifie le formulaire de connexion et connecte l'utilisateur
     */
    public function postSignIn($request, $response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if(!$auth){
            $this->flash->addMessage('danger', 'You can\'t signin');
            return $response->withRedirect($this->router->pathFor("auth.signin"));
        }

        $this->flash->addMessage('info', 'You\'re now logged');

        return $response->withRedirect($this->router->pathFor("homepage"));
    }

    public function getSignOut($request, $response)
    {
        $this->auth->logout();
        return $response->withRedirect($this->router->pathFor("homepage"));
    }

    /*
     * Affiche le formulaire de changement de mot de passe.
     */
    public function getChangePassword($request, $response)
    {
        return $this->view->render($response, 'auth/changepassword.html.twig');
    }

    /*
     * Verifie le formulaire de changement de mot de passe et
     */
    public function postChangePassword($request, $response)
    {
        $validation = $this->validator->validate($request, array(
            'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($this->auth->user()->password),
            'password' => v::noWhitespace()->notEmpty()
        ));

        if($validation->failed()){
            return $this->view->render($response, 'auth/changepassword.html.twig');
        }

        $this->auth->user()->setPassword($request->getParam('password'));

        // flash
        $this->flash->addMessage('info', 'Your password was changed');

        // redirect
        return $response->withRedirect($this->router->pathFor("homepage"));
    }
}