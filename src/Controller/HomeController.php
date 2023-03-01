<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Services\TrickService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @property TrickService
     */
    private TrickService $trickService;
    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;

    public function __construct(TrickService $trickService, ImageRepository $imageRepository,)
    {
        $this->trickService = $trickService;
        $this->imageRepository = $imageRepository;
    }

    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {

        //initialisation du repository demandé
        $images = $this->imageRepository->findby(['Trick' => null, 'user' => null ]);
        //Todo transformer en image service
        $tricks = $this->trickService->getTricksByPage(1);
        //TODO transformer en trick service
        return $this->render('pages/home.html.twig', [
                'page' => '1',
                'tricks' => $tricks,
                'images' => $images,
                'current_menu'=>'home',
            ]
        );
    }

    #[Route(path: '/moretricks', name: 'moretricks')]
    public function moreTrick(Request $request): Response
    {
        dump($request);
        $page = $request->query->getInt("page");
        if($page <= 1){
            $page = 2;
        }
        $tricks = $this->trickService->getTricksByPage($page);
        return $this->render('parts/front/fortricks.html.twig', [
                'tricks' => $tricks,
                'page' => $page
            ]
        );
    }
}