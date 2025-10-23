<?php

namespace App\Presentation\EntryPoint\Controller\Client;

use App\Application\Client\Connector\Command\EntityCreation\ClientEntityCreationCommand;
use App\Application\Exception\ValidationException;
use App\Application\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Presentation\EntryPoint\Data\Client\DTO\ClientEntityCreationData;
use App\Presentation\Form\Client\ClientRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ClientRegistrationController extends AbstractController
{
    #[Route('/client/registration', name: 'app_client_registration', methods: ['GET', 'POST'])]
    public function __invoke(
        Request                     $request,
        ClientEntityCreationCommand $creationCommand,
        TranslatorInterface         $translator,
    ): Response
    {
        $creationData = new ClientEntityCreationData();

        $form = $this->createForm(ClientRegistrationType::class, $creationData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $creationCommand->execute($creationData);

                $this->addFlash('success', $translator->trans('scoring.registration_success', ['%score%' => $data->getScore()]));

                return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
            } catch (ValidationException $exception) {
                foreach ($exception->getErrors() as $error) {
                    $form->get($error->getProperty())->addError(new FormError($error->getMessage()));
                }
            } catch (EntityManagerException) {
                $this->addFlash('danger', $translator->trans('error.server'));
            } catch (PhoneOperatorException) {
                $this->addFlash('danger', $translator->trans('scoring.error.phone_operator'));
            }
        }

        return $this->render('client/registration.html.twig', [
            'form' => $form,
        ]);
    }
}
