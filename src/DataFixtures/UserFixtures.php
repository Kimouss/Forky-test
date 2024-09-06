<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface  $passwordHasher,
    )
    {
    }
    public function load(ObjectManager $manager): void
    {
        $userProfile = $this->getReference(ProfileFixtures::PROFILE_USER);
        $user = new User();
        $user
            ->setEmail('forky@mail.com')
            ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
            ->setRoles($userProfile->getRoles())
            ->setProfile($userProfile)
        ;


        $adminProfile = $this->getReference(ProfileFixtures::PROFILE_ADMIN);
        $admin = new User();
        $admin
            ->setEmail('admin@mail.com')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
            ->setRoles($adminProfile->getRoles())
            ->setProfile($adminProfile)
        ;

        $manager->persist($user);
        $manager->persist($admin);
        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            ProfileFixtures::class,
        ];
    }
}
