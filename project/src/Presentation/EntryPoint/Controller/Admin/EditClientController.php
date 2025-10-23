<?php

namespace App\Presentation\EntryPoint\Controller\Admin;

use App\Application\Client\Connector\Command\EntityUpdate\ClientEntityUpdateCommand;
use App\Application\Client\Connector\Query\ClientEntity\ClientEntityQuery;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Application\Exception\ValidationException;
use App\Application\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Presentation\EntryPoint\Data\Client\DTO\ClientEntityUpdateData;
use App\Presentation\Form\Client\ClientEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EditClientController extends AbstractController
{
    #[Route('/admin/client/{id}/edit', name: 'admin_edit_client', methods: ['GET', 'POST'])]
    public function __invoke(
        Request                   $request,
        ClientEntityQuery         $clientEntityQuery,
        ClientEntityUpdateCommand $updateCommand,
        TranslatorInterface       $translator,
        int                       $id,
    ): Response
    {
        $data = new ClientEntityUpdateData($id);

        if ($request->isMethod('GET')) {
            try {
                $client = $clientEntityQuery->execute($id, true);
            } catch (ClientEntityNotFoundException $e) {
                throw $this->createNotFoundException($e->getMessage());
            }

            $data
                ->setPhone($client->getProfile()->getPhone())
                ->setEmail($client->getProfile()->getEmail())
                ->setFirstName($client->getProfile()->getFirstName())
                ->setLastName($client->getProfile()->getLastName())
                ->setEducation($client->getEducation())
                ->setConsentPersonalData($client->getConsentPersonalData());
        }

        $form = $this->createForm(ClientEditType::class, $data);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $client = $updateCommand->execute($data);

                $this->addFlash('success', $translator->trans('client.updated', ['%id%' => $client->getId()]));
                return $this->redirectToRoute('admin_clients', [], Response::HTTP_SEE_OTHER);
            }

        } catch (ClientEntityNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        } catch (ValidationException $e) {
            foreach ($e->getErrors() as $error) {
                $form->get($error->getProperty())->addError(new FormError($error->getMessage()));
            }
        } catch (EntityManagerException) {
            $this->addFlash('danger', $translator->trans('error.server'));
        } catch (PhoneOperatorException) {
            $this->addFlash('danger', $translator->trans('scoring.error.phone_operator'));
        }

        return $this->render('admin/editClient.html.twig', [
            'form' => $form,
        ]);
    }
}
