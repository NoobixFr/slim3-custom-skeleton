<?php

namespace App\Controllers;

use App\Models\User;

class AppController extends Controller{

    public function homepage($request, $response){
        return $this->view->render($response, 'app/homepage.html.twig');
    }

}