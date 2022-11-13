<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{

    public function index() : Response
    {
        return $this->render( 'index.html.twig' );
    }
}