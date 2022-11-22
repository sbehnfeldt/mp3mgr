<?php

namespace App\Controller;


use App\Service\Importer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{

    private Importer $importer;

    /**
     * @return Response
     */
    #[Route(
        '/',
        name: 'homepage',
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route(
        '/import',
        name: 'import',
        methods: ['GET']
    )]
    public function import(Importer $importer): Response
    {
        $dir = $this->getParameter('app.music_dir');
        $importer->import($dir);
        return $this->render('import.html.twig');
    }
}
