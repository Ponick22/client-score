<?php

namespace App\Presentation\EntryPoint\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    #[Route(path: '/', name: 'app_index', methods: ['GET'])]
    public function __invoke(
        #[MapQueryParameter] string $name = '',
    ): Response
    {
        return $this->render('app/index.html.twig', [
            'name' => $name,
        ]);
    }
}
