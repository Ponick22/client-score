<?php

namespace App\Presentation\Form\Client;

use App\Presentation\Form\Client\DTO\ClientRegistrationFormData;
use App\Presentation\Form\Client\Type\EducationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'client.email',
            ])
            ->add('phone', TextType::class, [
                'label' => 'client.phone',
            ])
            ->add('first_name', TextType::class, [
                'label' => 'client.firstname',
            ])
            ->add('last_name', TextType::class, [
                'label' => 'client.lastname',
            ])
            ->add('education', EducationType::class, [
                'label' => 'client.education',
            ])
            ->add('consent_personal_data', CheckboxType::class, [
                'required' => false,
                'label'    => 'client.personal_consent_personal_data',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'scoring.submit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClientRegistrationFormData::class,
        ]);
    }
}
