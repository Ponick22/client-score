<?php

namespace App\Presentation\EntryPoint\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminController extends AbstractController
{
    #[Route(path: '/admin', name: 'admin_index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->redirectToRoute('admin_clients', [], Response::HTTP_SEE_OTHER);
    }
}
