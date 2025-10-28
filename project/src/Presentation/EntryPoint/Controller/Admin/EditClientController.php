<?php

namespace App\Presentation\EntryPoint\Controller\Admin;

use App\Application\Client\Connector\Command\EntityUpdate\ClientEntityUpdateCommand;
use App\Application\Client\Connector\Query\ClientEntity\ClientEntityQuery;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Application\Exception\ValidationException;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\Profile\Exception\ProfileEmailInvalidException;
use App\Domain\Profile\Exception\ProfileFirstNameInvalidException;
use App\Domain\Profile\Exception\ProfileLastNameInvalidException;
use App\Domain\Profile\Exception\ProfilePhoneInvalidException;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Presentation\EntryPoint\Data\Client\DTO\ClientEntityUpdateData;
use App\Presentation\Form\Client\ClientEditType;
use App\Presentation\Form\Client\DTO\ClientEditFormData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EditClientController extends AbstractController
{
    private FormInterface $form;

    public function __construct(
        private readonly ClientEntityUpdateCommand $updateCommand,
        private readonly TranslatorInterface       $translator,
    ) {}

    #[Route('/admin/client/{id}/edit', name: 'admin_edit_client', methods: ['GET', 'POST'])]
    public function __invoke(
        Request           $request,
        ClientEntityQuery $clientEntityQuery,
        int               $id,
    ): Response
    {
        $formData = new ClientEditFormData($id);

        if ($request->isMethod('GET')) {
            try {
                $client = $clientEntityQuery->execute($id, true);
            } catch (ClientEntityNotFoundException $e) {
                throw $this->createNotFoundException($e->getMessage());
            }

            $formData
                ->setPhone($client->getProfile()->getPhone())
                ->setEmail($client->getProfile()->getEmail())
                ->setFirstName($client->getProfile()->getFirstName())
                ->setLastName($client->getProfile()->getLastName())
                ->setEducation($client->getEducation())
                ->setConsentPersonalData($client->getConsentPersonalData());
        }

        $this->form = $this->createForm(ClientEditType::class, $formData);
        $this->form->handleRequest($request);

        if (
            $this->form->isSubmitted() &&
            $this->form->isValid() &&
            $clientData = $this->handleUpdateClient($formData)
        ) {
            $this->addFlash('success', $this->translator->trans('client.updated', ['%id%' => $clientData->getId()]));

            return $this->redirectToRoute('admin_show_client', ['id' => $clientData->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/editClient.html.twig', [
            'form' => $this->form,
        ]);
    }

    private function handleUpdateClient(ClientEditFormData $formData): ?ClientOutputData
    {
        try {
            $updateData = new ClientEntityUpdateData(
                $formData->getId(),
                new ProfileEmail($formData->getEmail()),
                new ProfilePhone($formData->getPhone()),
                new ProfileFirstName($formData->getFirstName()),
                new ProfileLastName($formData->getLastName()),
                $formData->getEducation(),
                $formData->getConsentPersonalData(),
            );

            return $this->updateCommand->execute($updateData);
        } catch (ValidationException $exception) {
            $errors = [];
            foreach ($exception->getErrors() as $error) {
                $errors[$error->getProperty()] = $error->getMessage();
            }

            foreach (array_unique($errors) as $property => $message) {
                $this->formSetError($property, $message);
            }
        } catch (ProfileEmailInvalidException $e) {
            $this->formSetError('email', $e->getMessage());
        } catch (ProfilePhoneInvalidException $e) {
            $this->formSetError('phone', $e->getMessage());
        } catch (ProfileFirstNameInvalidException $e) {
            $this->formSetError('firstName', $e->getMessage());
        } catch (ProfileLastNameInvalidException $e) {
            $this->formSetError('lastName', $e->getMessage());
        } catch (ClientEntityNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        } catch (EntityManagerException) {
            $this->addFlash('danger', $this->translator->trans('error.server'));
        } catch (PhoneOperatorException) {
            $this->addFlash('danger', $this->translator->trans('scoring.error.phone_operator'));
        }

        return null;
    }

    private function formSetError(string $property, string $message): void
    {
        $this->form->get($property)->addError(new FormError($this->translator->trans($message, [], 'validators')));
    }
}
