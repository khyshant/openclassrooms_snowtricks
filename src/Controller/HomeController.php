<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
        $tricks = $this->trickRepository->getAllTricks(1);
        return $this->render('pages/home.html.twig', [
                'page' => '1',
                'tricks' => $tricks,
                'images' => $images,
                'uploadDirFixtures' => $this->uploadDirFixtures,
                'current_menu'=>'home',
            ]
        );
    }

    #[Route(path: '/moretricks', name: 'moretricks')]
    public function moreTrick(Request $request): Response
    {
        $page = $request->query->getInt("page");
        if($page <= 1){
            $page = 2;
        }
        $tricks = $this->trickRepository->getAllTricks($page);
        return $this->render('parts/front/fortricks.html.twig', [
                'tricks' => $tricks,
                'page' => $page
            ]
        );
    }
}