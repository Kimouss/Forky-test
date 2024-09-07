<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MenuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $parentHomeMenu = new Menu();
        $parentHomeMenu
            ->setLabel('Home')
            ->setActive(true)
            ->setPosition(100)
        ;
        $manager->persist($parentHomeMenu);
        $manager->flush();

        $homeMenu = new Menu();
        $homeMenu
            ->setLabel('Home index')
            ->setRoute('app_home')
            ->setParent($parentHomeMenu)
            ->setActive(true)
            ->setPosition(101)
        ;

        $otherMenu = new Menu();
        $otherMenu
            ->setLabel('Home other')
            ->setRoute('app_other')
            ->setParent($parentHomeMenu)
            ->setActive(true)
            ->setPosition(102)
        ;

        $manager->persist($homeMenu);
        $manager->persist($otherMenu);
        $manager->flush();
    }
}
