<?php

namespace App\DataFixtures;


use Faker\Factory;
use Faker\Generator;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("anth.blanchard@gmail.com");
        $user->setPassword('123456');
        $user->setRoles(['ADMIN','USER']);
        $user->setToken("testtoken");
        $user->setUsername("anthony");
        $manager->persist($user);
        $manyUsers = rand(5,25);
        for($i=2; $i<=$manyUsers; $i++){
            $user = new User();
            $user->setEmail("user".$i."@test.com");
            $user->setPassword('123456');
            $user->setRoles(['USER']);
            $user->setToken("testtoken");
            $user->setUsername("user".$i);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
