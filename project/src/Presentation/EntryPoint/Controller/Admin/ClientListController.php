<?php

namespace App\Presentation\EntryPoint\Controller\Admin;

use App\Application\Client\Connector\Query\ClientEntityList\ClientEntityCountByFilterQuery;
use App\Application\Client\Connector\Query\ClientEntityList\ClientEntityListByFilterQuery;
use App\Presentation\EntryPoint\Data\Client\DTO\ClientEntityListByFilterData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class ClientListController extends AbstractController
{
    #[Route(path: '/admin/client', name: 'admin_clients', methods: ['GET'])]
    public function __invoke(
        ClientEntityListByFilterQuery  $clientEntityListByFilterQuery,
        ClientEntityCountByFilterQuery $clientEntityCountByFilterQuery,
        #[MapQueryParameter] int       $page = 1,
        #[MapQueryParameter] int       $limit = 10,
    ): Response
    {
        $page   = max(1, $page);
        $limit  = max(1, $limit);
        $offset = ($page - 1) * $limit;

        $clientFilter = new ClientEntityListByFilterData();

        $totalClients = $clientEntityCountByFilterQuery->execute($clientFilter);

        $clientFilter
            ->setLimit($limit)
            ->setOffset($offset);

        $clients = $clientEntityListByFilterQuery->execute($clientFilter);

        $totalPages = ceil($totalClients / $limit);

        return $this->render('admin/index.html.twig', [
            'clients'     => $clients,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'limit'       => $limit,
        ]);
    }
}
