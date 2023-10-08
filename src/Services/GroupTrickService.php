<?php

namespace App\Services;

use App\Repository\GroupTrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupTrickService extends AbstractController
{
    private $doctrine;
    private GroupTrickRepository $grouptrickRepository;

    public function __construct(ManagerRegistry $doctrine, GroupTrickRepository $repository){
        $this->doctrine = $doctrine;
        $this->grouptrickRepository =$repository;
    }


    public function deleteGroupTrick($grouptrick) {
        $this->doctrine->getManager()->remove($grouptrick);
        $this->doctrine->getManager()->flush();
    }
}