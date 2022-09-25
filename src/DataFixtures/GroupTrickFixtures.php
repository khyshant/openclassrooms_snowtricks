<?php

namespace App\DataFixtures;


use Faker\Factory;
use Faker\Generator;
use App\Entity\GroupTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupTrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manyGroups = rand (2,10);
        for($i = 1 ; $i <= $manyGroups ; $i++){
            $group = new GroupTrick();
            $group->setName("group".$i);
            $manager->persist($group);
        }
        $manager->flush();
    }
}
