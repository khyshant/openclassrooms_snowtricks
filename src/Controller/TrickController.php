<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Services\trickService;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TrickController extends AbstractController
{

    /**
     * @var string
     */
    private string $uploadDirFixtures;

    /**
     * @property TrickRepository trickRepository
     */
    private TrickRepository $trickRepository;

    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;

    /**
     * @property TrickRepository trickRepository
     */
    private trickService $trickService;

    /**
     * @param TrickRepository $trickRepository
     * @param ImageRepository $imageRepository
     * @param string $uploadDirFixtures
     * @param trickService $trickService
     */
    public function __construct(string $uploadDirFixtures, trickService $trickService)
    {
        $this->uploadDirFixtures = $uploadDirFixtures;
        $this->trickService = $trickService;
    }



    #[Route(path: '/show/{slug}', name: 'trick.show')]
    public function show( Trick $trick): Response
    {
        return $this->render('pages/trick_show.html.twig',[
                'trick' => $trick
            ]
        );
    }

    #[Route(path: '/delete/{slug}', name: 'trick.delete')]
    public function delete( Trick $trick): Response
    {
        $this->trickService->deleteTrick($trick);
        return $this->redirectToRoute('home');
    }

}