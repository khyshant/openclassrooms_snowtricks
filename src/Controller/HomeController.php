<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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

    public function __construct(TrickRepository $trickRepository, ImageRepository $imageRepository,string $uploadDirFixtures)
    {
        $this->trickRepository = $trickRepository;
        $this->imageRepository = $imageRepository;
        $this->uploadDirFixtures = $uploadDirFixtures;
    }

    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        //initialisation du repository demandé
        $images = $this->imageRepository->findby(['Trick' => null, 'user' => null ]);
        $tricks = $this->trickRepository->findAll();
        return $this->render('pages/home.html.twig', [
                'tricks' => $tricks,
                'images' => $images,
                'uploadDirFixtures' => $this->uploadDirFixtures,
                'current_menu'=>'home',
            ]
        );
    }
}