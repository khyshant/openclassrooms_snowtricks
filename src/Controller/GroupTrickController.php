<?php

namespace App\Controller;

use App\Entity\GroupTrick;
use App\Handler\GroupTrickHandler;
use App\Security\voter\GroupTrickVoter;
use App\Services\GroupTrickService;
use App\Repository\GroupTrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class GroupTrickController extends AbstractController
{

    /**
     * @property TrickService
     */
    private GroupTrickService $grouptrickService;

    /**
     * @param $grouptrickService
     */
    public function __construct(GroupTrickService $grouptrickService)
    {
        $this->grouptrickService = $grouptrickService;
    }


    #[Route(path: 'grouptrick/update/{id}', name: 'grouptrick.update')]
    public function update(Request $request, GroupTrick $grouptrick,GroupTrickHandler $handler): Response
    {
        $this->denyAccessUnlessGranted(GroupTrickVoter::EDIT, $grouptrick);
        if($handler->handle($request, $grouptrick)) {
            return $this->redirectToRoute("grouptrick.list");
        }
        return $this->render("pages/grouptrick_update.html.twig", [
            "trick" => $grouptrick,
            "form" => $handler->createView()
        ]);
    }
    
    #[Route(path: '/grouptrick/create', name: 'grouptrick.create')]
    public function create(Request $request, GroupTrickHandler $handler): Response
    {        
        $grouptrick = new GroupTrick();
        $this->denyAccessUnlessGranted(GroupTrickVoter::EDIT, $grouptrick);
        if($handler->handle($request, $grouptrick)) {
            return $this->redirectToRoute("grouptrick.list");
        }
        return $this->render("pages/grouptrick_update.html.twig", [
            "grouptrick" => $grouptrick,
            "form" => $handler->createView()
        ]);
    }

    #[Route(path: 'grouptrick/list/', name: 'grouptrick.list')]
    public function list(GroupTrickRepository $groupTrickRepository): Response
    {
        $grouptricks = $groupTrickRepository->findAll();
        //TODO transformer en trick service
        return $this->render('pages/grouplist.html.twig', [
                'page' => '1',
                'grouptricks' => $grouptricks,
            ]
        );
    }


    #[Route(path: 'grouptrick/delete/{id}', name: 'grouptrick.delete')]
    public function delete(GroupTrick $grouptrick): Response
    {
        $this->grouptrickService->deletegroupTrick($grouptrick);
        return $this->redirectToRoute('grouptrick.list');

    } 
}