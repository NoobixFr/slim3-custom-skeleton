<?php

namespace App\Controllers;

class AppController extends Controller{

    public function homepage($request, $response){
        return $this->view->render($response, 'homepage.html.twig');
    }

}