<?php

namespace App\Presentation\EntryPoint\Controller\Admin;

use App\Application\Client\Connector\Query\ClientEntity\ClientEntityQuery;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowClientController extends AbstractController
{
    #[Route('/admin/client/{id}', name: 'admin_show_client', methods: ['GET'])]
    public function __invoke(
        Request           $request,
        ClientEntityQuery $clientEntityQuery,
        int               $id
    ): Response
    {
        $userTimezone = $request->cookies->get('user_timezone', 'UTC');

        try {
            $client = $clientEntityQuery->execute($id, true);
        } catch (ClientEntityNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->render('admin/showClient.html.twig', [
            'client'       => $client,
            'userTimezone' => $userTimezone,
        ]);
    }
}
