<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixtures extends Fixture
{
    public const PROFILE_USER = 'PROFILE_USER';
    public const PROFILE_ADMIN = 'PROFILE_ADMIN';

    public function load(ObjectManager $manager): void
    {
        $userProfile = new Profile();
        $userProfile
            ->setName('Utilisateur')
            ->setRoles(['ROLE_USER'])
        ;
        $manager->persist($userProfile);

        $adminProfile = new Profile();
        $adminProfile
            ->setName('Administrateur')
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($adminProfile);
        $manager->flush();

        $this->addReference(self::PROFILE_USER, $userProfile);
        $this->addReference(self::PROFILE_ADMIN, $adminProfile);
    }
}
