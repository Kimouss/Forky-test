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
            ->setVisible(true)
        ;
        $manager->persist($parentHomeMenu);
        $manager->flush();

        $homeMenu = new Menu();
        $homeMenu
            ->setLabel('Home index')
            ->setRoute('app_home_index')
            ->setParent($parentHomeMenu)
            ->setActive(true)
            ->setPosition(101)
            ->setVisible(true)
        ;

        $otherMenu = new Menu();
        $otherMenu
            ->setLabel('Home other')
            ->setRoute('app_home_other')
            ->setParent($parentHomeMenu)
            ->setActive(true)
            ->setPosition(102)
            ->setVisible(true)
        ;

        $testMenu = new Menu();
        $testMenu
            ->setLabel('Test')
            ->setRoute('app_test_index')
            ->setActive(true)
            ->setPosition(200)
            ->setVisible(true)
        ;

        $manager->persist($homeMenu);
        $manager->persist($otherMenu);
        $manager->persist($testMenu);
        $manager->flush();
    }
}
