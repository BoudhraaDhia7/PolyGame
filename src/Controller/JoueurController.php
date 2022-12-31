<?php

namespace App\Controller;

use App\Entity\Joueur;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JoueurController extends AbstractController
{
    #[Route('/joueur', name: 'app_joueur')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/JoueurController.php',
        ]);
    }
    #[Route('/joueur/ajouter', name: 'ajouter_joueur')]
    public function ajouter_joueur(ManagerRegistry $doctrine): JsonResponse
    {
        $date=New DateTime();
        
        $entityManager = $doctrine->getManager();
           
        $Joueur = new Joueur();
        $Joueur->setNom('Dhia');
        $Joueur->setEmail("boudhraad@gmail.com");
        $Joueur->setBornAt($date);

        // tell Doctrine you want to (eventually) save the Joueur (no queries yet)
        $entityManager->persist($Joueur);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        
        
        return $this->json([
            'message' => 'Success !!!',
            'path' => 'src/Controller/JoueurController.php',
        ]);
    }
    
    #[Route('/joueur/show', name: 'ajouter_joueur')]
    public function show_joueur(ManagerRegistry $doctrine): JsonResponse
    {
        
        $Joueur = $doctrine->getRepository(Joueur::class)->findAll();

        if (!$Joueur) {
            throw $this->createNotFoundException(
                'No Joueur found'
            );
        }

        dd($Joueur);
        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
        
        return $this->json([
            'message' => 'Success !!!',
            'path' => 'src/Controller/JoueurController.php',
        ]);
    }
}
