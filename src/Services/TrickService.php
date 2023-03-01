<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickService extends AbstractController
{
    private $doctrine;
    private TrickRepository $trickRepository;

    public function __construct(ManagerRegistry $doctrine, TrickRepository $repository){
        $this->doctrine = $doctrine;
        $this->trickRepository =$repository;
    }


    public function deleteTrick($trick) {
        $this->doctrine->getManager()->remove($trick);
        $this->doctrine->getManager()->flush();
    }

    public function getTricksByPage($page) {
        return $this->trickRepository->getAllTricks($page);
    }


    public function getTricksByAuthor($page, User $user) {
        return $this->trickRepository->getTricksByAuthor($page,$user);
    }
}