<?php

namespace App\Controller;

use App\Entity\Game;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommenController extends AbstractController
{
    //Rendring the admin dashboard
    #[Route('/dashboard', name: 'dashboard')]
    public function index_dashboard(): Response
    {
        return $this->render('admin/templates/dashboard.html.twig');
    }

    //Rendring the web-app index
    #[Route('/', name: 'index' )]
    public function index(Security $security ,ManagerRegistry $doctrine): Response
    {
        $user = $security->getUser();
        $data_table=[];
        if($user){
                $data_table[0]=
            [
                'titre'=>$user->getEditeur()->getTitre(),
                'id'=>$user->getEditeur()->getId(),
            ];
           
            return $this->render('joueur/templates/index.html.twig' ,array(
                'games'=>$data_table,
            ));

        }
        $game = $doctrine->getRepository(Game::class)->findAll();
        foreach($game as $item)
        {
          
            $t=[
                'titre'=>$item->getTitre(),
                'id'=>$item->getId(),
            ];
            array_push($data_table,$t);
        }
        
        return $this->render('joueur/templates/index.html.twig',array(
            'games'=>$data_table,
        ));
    }
}
