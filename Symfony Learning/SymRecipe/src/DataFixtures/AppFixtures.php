<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName('Ingredient ' . $i)
                ->setPrice(mt_rand(0, 100));
            
            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}
