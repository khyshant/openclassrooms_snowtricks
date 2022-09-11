<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Faker\Generator;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        $user = new User();
        $user->setEmail("anth.blanchard@gmail.com");
        $user->setPassword('123456');
        $user->setRoles(['ADMIN','USER']);
        $user->setToken("testtoken");
        $user->setUsername("anthony");
        $manager->persist($user);

        $manager->flush();
    }
}
