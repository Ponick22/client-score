<?php

namespace App\Infrastructure\Doctrine\DataFixtures;

use App\Domain\Client\Enum\EducationEnum;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\Enum\UserRoleEnum;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;
use App\Infrastructure\Doctrine\Entity\Client;
use App\Infrastructure\Doctrine\Entity\Profile;
use App\Infrastructure\Doctrine\Entity\User;
use App\Infrastructure\Security\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly PasswordHasher $hasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $this->createAdmins($manager);
        $this->createClients($manager);

        $manager->flush();
    }

    private function createClients(ObjectManager $manager)
    {
        for ($i = 1; $i < 10; $i++) {
            $email = "user$i@example.com";
            $user  = $this->createUser($email, [UserRoleEnum::Client]);

            $profile = $this->createProfile(
                $user,
                $email,
                sprintf('89%d23456789', $i),
                "John$i",
                "Doe$i"
            );

            $client = $this->createClient(
                $profile,
                EducationEnum::Special,
                PhoneOperator::fromPhoneOperator('Beeline'),
                true,
                $i
            );

            $manager->persist($client);
        }
    }

    private function createAdmins(ObjectManager $manager)
    {
        $adminUser = $this->createUser(
            'admin@example.com',
            [UserRoleEnum::Admin],
            'Password123'
        );

        $manager->persist($adminUser);

        $adminUser2 = $this->createUser(
            'admin2@example.com',
            [UserRoleEnum::Admin],
            'Password123'
        );

        $adminProfile = $this->createProfile(
            $adminUser2,
            'admin2@example.com',
            '89876543210',
            'Admin',
        );

        $manager->persist($adminProfile);
    }


    private function createUser(string $email, array $roles = [], ?string $password = null): User
    {
        $user = new User();
        $user
            ->setEmail(new UserEmail($email))
            ->setRoles(new UserRoleEnumCollection($roles));

        if ($password) {
            $user->setPassword($this->hasher->hash($user, new UserPassword($password)));
        }

        return $user;
    }

    private function createProfile(
        User    $user,
        string  $email,
        string  $phone,
        ?string $firstName = null,
        ?string $lastName = null,
    ): Profile
    {
        $profile = new Profile();
        $profile
            ->setUser($user)
            ->setEmail(new ProfileEmail($email))
            ->setPhone(new ProfilePhone($phone))
            ->setFirstName($firstName ? new ProfileFirstName($firstName) : null)
            ->setLastName($lastName ? new ProfileLastName($lastName) : null);

        return $profile;
    }

    private function createClient(
        Profile       $profile,
        EducationEnum $education,
        PhoneOperator $phoneOperator,
        bool          $consentPersonalData,
        int           $score
    ): Client
    {
        $client = new Client();
        $client
            ->setProfile($profile)
            ->setEducation($education)
            ->setPhoneOperator($phoneOperator)
            ->setConsentPersonalData($consentPersonalData)
            ->setScore($score);

        return $client;
    }
}
