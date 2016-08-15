<?php

namespace App\Auth;

use App\Models\User;

class Auth
{

    public function user()
    {
        if($this->check()){
            return User::find($_SESSION['user']);
        }

        return false;
    }

    public function check()
    {
        return isset( $_SESSION['user']);
    }

    public function attempt($email, $password)
    {
        // On recupere l'utilisateur via son email
        $currentUser = User::where('email', $email)->first();

        // Si on ne peut récupérer l'utilisateur (il n'extiste pas) return false
        if(!$currentUser){
            return false;
        }

        // Sinon on vérifie le mot de passe saisie pour cet utilisateur.
        if(password_verify($password, $currentUser->password)){
            $_SESSION['user'] = $currentUser->id;
            return true;
        }

        // Si le mot de passe est incorrect on return false;
        return false;
    }

    public function logout(){
        unset($_SESSION['user']);
    }
}