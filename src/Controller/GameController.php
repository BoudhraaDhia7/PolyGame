<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\AjoutJeuType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GameController extends AbstractController
{
    
    //rendring the game to the user
    #[Route('/render/{id}', name: 'game_render')]
    public function game_render(ManagerRegistry $doctrine, $id): Response
    {
        $game = $doctrine->getRepository(Game::class)->find($id);
        return $this->render('game/index.html.twig',array(
            "game"=>'/game/'.$game->getTitre(). '/index.html.twig'
        ));
    }
    

    //Add the game to the dataBase and splitting the files based on the extension
    #[Route('/game/ajouter', name: 'ajouter_game')]
    public function ajouter_game(ManagerRegistry $doctrine,Request $request): Response
    {   
        $game = new Game();
        $form = $this->createForm(AjoutJeuType::class,$game);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {  
            $data = $form->getData();
            foreach ($data->getFiles() as $file) {
                if($file->getClientOriginalName()=='bg.PNG')
                {   
                        $file->move($this->getParameter('user_directory'),
                        str_replace($file->getClientOriginalName(),'.PNG','').'bg_'.$game->getTitre().'.png');
                }else

                if(!strpos($file->getClientOriginalName(),'.html'))
                {
                        $file->move($this->getParameter('uploads_directory').'/'.$game->getTitre(),
                        $file->getClientOriginalName());
                }else  
                    {
                       
                        $file->move($this->getParameter('templates_directory').'/'.$game->getTitre(),
                        $file->getClientOriginalName().'.twig');
                    }
                
            }
            $game->setFiles([]);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($game);
            $entityManager->flush();
            return $this->redirectToRoute('update_game');
           
        }
        return $this->renderForm('admin/templates/ajouter.html.twig', [
            'form' => $form,
        ]);
       
    }
    
    //rendr game update
    #[Route('/update/game', name: 'update_game')]
    public function update_game(ManagerRegistry $doctrine): Response
    {   
        $game = $doctrine->getRepository(Game::class)->findAll();
        return $this->renderForm('admin/templates/updateGame.html.twig',[
            "games"=>$game,
        ]);
    }

    //render game update
    #[Route('/update/game/edit/{id}', name: 'save_game')]
    public function save_game(ManagerRegistry $doctrine,Request $request ,$id): Response
    {   
        $entityManager = $doctrine->getManager();
        $game = $entityManager->getRepository(Game::class)->find($id);
        $form = $this->createForm(AjoutJeuType::class,$game);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {  
            $game->setTitre($form->get('titre')->getData());
            $game->setType($form->get('type')->getData());
            $game->setNbJoueur($form->get('nb_joueur')->getData());
            $game->setEditeur($form->get('editeur')->getData());
            $game->setFiles([]);
            $entityManager->flush();
            return $this->redirectToRoute('update_game');
        }
        return $this->renderForm('admin/templates/updateGameModel.html.twig',[
            "form"=>$form,
        ]);
    }

    //delete game row
    #[Route('/update/game/delete/{id}', name: 'delete_game')]
    public function delete_game(ManagerRegistry $doctrine ,$id): Response
    {   
        $entityManager = $doctrine->getManager();
        $game = $entityManager->getRepository(Game::class)->find($id);
        $entityManager->remove($game);
        $entityManager->flush();
        return $this->redirectToRoute('update_game');
    }
}
