<?php

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class trickService extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
    }
    public function deleteTrick($trick) {
        $this->doctrine->getManager()->remove($trick);
        $this->doctrine->getManager()->flush();
    }
}