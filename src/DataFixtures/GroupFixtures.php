<?php

namespace App\DataFixtures;


use Faker\Factory;
use Faker\Generator;
use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manyGroups = rand (2,10);
        for($i = 1 ; $i <= $manyGroups ; $i++){
            $group = new Group();
            $group->setName("group".$i);
            $manager->persist($group);
        }
        $manager->flush();
    }
}
