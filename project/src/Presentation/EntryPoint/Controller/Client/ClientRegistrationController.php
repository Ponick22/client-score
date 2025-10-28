<?php

namespace App\Presentation\EntryPoint\Controller\Client;

use App\Application\Client\Connector\Command\EntityCreation\ClientEntityCreationCommand;
use App\Application\Client\DTO\ClientOutputData;
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
use App\Domain\User\Exception\UserEmailInvalidException;
use App\Domain\User\ValueObject\UserEmail;
use App\Presentation\EntryPoint\Data\Client\DTO\ClientEntityCreationData;
use App\Presentation\Form\Client\ClientRegistrationType;
use App\Presentation\Form\Client\DTO\ClientRegistrationFormData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ClientRegistrationController extends AbstractController
{
    private FormInterface $form;

    public function __construct(
        private readonly ClientEntityCreationCommand $creationCommand,
        private readonly TranslatorInterface         $translator,
    ) {}

    #[Route('/client/registration', name: 'app_client_registration', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $formData = new ClientRegistrationFormData();

        $this->form = $this->createForm(ClientRegistrationType::class, $formData);
        $this->form->handleRequest($request);

        if (
            $this->form->isSubmitted() &&
            $this->form->isValid() &&
            $clientData = $this->handleRegistrationClient($formData)
        ) {
            $this->addFlash('success', $this->translator->trans('scoring.registration_success', ['%score%' => $clientData->getScore()]));

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/registration.html.twig', [
            'form' => $this->form,
        ]);
    }

    private function handleRegistrationClient(ClientRegistrationFormData $formData): ?ClientOutputData
    {
        try {
            $creationData = new ClientEntityCreationData(
                new UserEmail($formData->getEmail()),
                new ProfileEmail($formData->getEmail()),
                new ProfilePhone($formData->getPhone()),
                new ProfileFirstName($formData->getFirstName()),
                new ProfileLastName($formData->getLastName()),
                $formData->getEducation(),
                $formData->getConsentPersonalData(),
            );

            return $this->creationCommand->execute($creationData);
        } catch (ValidationException $exception) {
            $errors = [];
            foreach ($exception->getErrors() as $error) {
                $errors[$error->getProperty()] = $error->getMessage();
            }

            foreach (array_unique($errors) as $property => $message) {
                $this->formSetError($property, $message);
            }
        } catch (ProfileEmailInvalidException|UserEmailInvalidException $e) {
            $this->formSetError('email', $e->getMessage());
        } catch (ProfilePhoneInvalidException $e) {
            $this->formSetError('phone', $e->getMessage());
        } catch (ProfileFirstNameInvalidException $e) {
            $this->formSetError('firstName', $e->getMessage());
        } catch (ProfileLastNameInvalidException $e) {
            $this->formSetError('lastName', $e->getMessage());
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
