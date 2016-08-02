<?php

$app->get('/', function($request, $response){
    return 'Home';
})->setName('homepage');