<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manyCommentUsers = rand (2,25);
        $manyComments = rand (2,10);
        $trickRepository = $manager->getRepository(Trick::class);
        $tricks = $trickRepository->findAll();
        for($i=1; $i<=$manyCommentUsers; $i++){
            $user = new User();
            $user->setEmail("commentUser".$i."@test.com");
            $user->setPassword('123456');
            $user->setRoles(['ROLE_USER']);
            $user->setToken("testtoken");
            $user->setUsername("commentUser".$i);
            $manager->persist($user);
        }
        $manager->flush();
        foreach($tricks as $trick){
            for($i = 1 ; $i <= $manyComments ; $i++){
                $userRepository = $manager->getRepository(User::class);
                $userNumber = rand(1,$manyCommentUsers);
                $user = $userRepository->findByUsername("commentUser".$userNumber);
                $comment = new Comment();
                $comment->setAuthor($user[0]);
                $comment->setTrick($trick);
                $comment->setComment("commentaire trick ".$trick->getId()." user ".$user[0]->getId());
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
