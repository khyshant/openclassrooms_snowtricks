<?php

namespace App\DataFixtures;


use App\Entity\GroupTrick;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->findAllUser();
        $GroupTrickRepository = $manager->getRepository(GroupTrick::class);
        $groups = $GroupTrickRepository->findAll();
        $countGroup  = count($groups);
        foreach($users as $user){
            $manyTricks = rand(5,25);
            for($i=1; $i<=$manyTricks; $i++){
                $group = $GroupTrickRepository->find(rand(1,$countGroup));
                $trick = new Trick();
                $trick->setTitle("test");
                $trick->setDescription("test description");
                $trick->setMetaDescription("test metat desc");
                $trick->setAuthor($user);
                $trick->setDateAdd("1970-01-01 00:00:01");
                $trick->setDateUpdate("1970-01-01 00:00:02");
                $trick->setMetaTitle("metaTitle");
                $trick->setGroupTrick($group);
                $trick->setSlug("test_".$user->getId()."_".$i);
                $trick->setValid(false) ;
                $manager->persist($trick);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            GroupTrickFixtures::class,
        ];
    }
}
