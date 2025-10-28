<?php

namespace App\Presentation\Form\Client\DTO;

use App\Domain\Client\Enum\EducationEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ClientRegistrationFormData
{
    #[Assert\Length(max: 255)]
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email;

    #[Assert\NotBlank]
    private string $phone;

    #[Assert\NotBlank]
    private string $firstName;

    #[Assert\NotBlank]
    private string $lastName;

    #[Assert\NotBlank]
    private EducationEnum $education;

    private bool $consentPersonalData;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEducation(): EducationEnum
    {
        return $this->education;
    }

    public function setEducation(EducationEnum $education): self
    {
        $this->education = $education;

        return $this;
    }

    public function getConsentPersonalData(): bool
    {
        return $this->consentPersonalData;
    }

    public function setConsentPersonalData(bool $consentPersonalData): self
    {
        $this->consentPersonalData = $consentPersonalData;

        return $this;
    }
}
