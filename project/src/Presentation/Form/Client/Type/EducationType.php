<?php

namespace App\Presentation\Form\Client\Type;

use App\Domain\Client\Enum\EducationEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EducationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_combine(
                array_map(fn(EducationEnum $enum): string => 'client.education_' . $enum->value, EducationEnum::cases()),
                EducationEnum::cases()
            ),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
