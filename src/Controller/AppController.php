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
        $this->importer = $importer;
        $dir = $this->getParameter('app.music_dir');
        $this->importDirectory($dir);
        return $this->render('index.html.twig');
    }

    private function importDirectory(string $dir)
    {
        $entries = scandir($dir);
        foreach ($entries as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }
            $pathname = implode(DIRECTORY_SEPARATOR, [$dir, $entry]);
            if (is_dir($pathname)) {
                $this->importDirectory($pathname);
            } elseif ( is_file($pathname)) {
                $this->importFile($pathname);
            } else {
//                throw new \Exception( 'What?!');
                continue;
            }
        }
    }

    private function importFile(string $pathname) : array
    {
        return $this->importer->importFile($pathname);
    }
}