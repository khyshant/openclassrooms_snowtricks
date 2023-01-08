<?php

namespace App\Services;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class commentService extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
    }
    public function GetTrickComment(trick $trick, int $page) {
        $trickComments = [];
        $i = 0;
        $commentRepository = $this->doctrine->getRepository(Comment::class);
        $userRepository = $this->doctrine->getRepository(User::class);
        $imageRepository = $this->doctrine->getRepository(Image::class);
        $comments = $commentRepository->getCommentsByTrick($trick, $page);

        //TODO : this function give a null user entity just an id.Same for avatar
        foreach ($comments as $comment){
            $trickComments[$i]['comment'] = $comment;
            $trickComments[$i]['author'] = $userRepository->findCommentUser($comment->getAuthor()->getId());
            $trickComments[$i]['avatar'] = $imageRepository->findAvatarByUser($comment->getAuthor()->getId());
            $i++;
        }
        return $trickComments;
    }
}