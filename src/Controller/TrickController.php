<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Services\commentService;
use App\Services\TrickService;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @property TrickRepository trickRepository
     */
    private TrickRepository $trickRepository;

    /**
     * @var ImageRepository
     */
    private ImageRepository $imageRepository;

    /**
     * @property TrickService
     */
    private TrickService $trickService;
    private commentService $commentService;

    /**
     * @param TrickService $trickService
     * @param commentService $commentService
     */
    public function __construct(TrickService $trickService, commentService $commentService,)
    {
        $this->trickService = $trickService;
        $this->commentService = $commentService;
    }

    #[Route(path: '/show/{slug}', name: 'trick.show')]
    public function show( Trick $trick): Response
    {
        return $this->render('pages/trick_show.html.twig',[
                'trick' => $trick,
                'displayedComments' => $this->commentService->GetTrickComment($trick,1)
            ]
        );
    }

    #[Route(path: '/delete/{slug}', name: 'trick.delete')]
    public function delete( Trick $trick): Response
    {
        $this->trickService->deleteTrick($trick);
        return $this->redirectToRoute('home');
    }

    #[Route(path: '/list/', name: 'trick.list')]
    public function list(): Response
    {

        $tricks = $this->trickService->getTricksByAuthor(1,$this->getUser());
        //TODO transformer en trick service
        return $this->render('pages/list.html.twig', [
                'page' => '1',
                'tricks' => $tricks,
            ]
        );
    }

    #[Route(path: '/moretricksByUser', name: 'moretricksByUser')]
    public function moretricksByUser(Request $request): Response
    {

        $page = $request->query->getInt("page");
        if($page <= 1){
            $page = 2;
        }
        $tricks = $this->trickService->getTricksByAuthor($page,$this->getUser());
        return $this->render('parts/front/trickbyauthor.html.twig', [
                'tricks' => $tricks,
                'page' => $page
            ]
        );
    }
}