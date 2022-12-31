<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    
    //add new user
    #[Route('/joueur/ajouter', name: 'ajouter_joueur')]
    public function ajouter_joueur(ManagerRegistry $doctrine,Request $request ,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $joueur = new User();
        $form = $this->createForm(RegistrationFormType::class,$joueur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {  
            $entityManager = $doctrine->getManager();
            $joueur->setPassword(
                $userPasswordHasher->hashPassword(
                    $joueur,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($joueur);
            $entityManager->flush();
            return $this->redirectToRoute('update_joueur');
        }
        return $this->renderForm('admin/templates/ajouterUser.html.twig', [
            'form' => $form,
        ]);
    }
    
    //render the user update 
    #[Route('/update/users', name: 'update_joueur')]
    public function update_joueur(ManagerRegistry $doctrine): Response
    {   
    
        $dates=[];
        $joueurs = $doctrine->getRepository(User::class)->findAll();
       foreach($joueurs as $joueur)
        {
            $t=[
                "email"=>$joueur->getEmail(),
                "nom"=>$joueur->getnom(),
                "born_at"=>$joueur->getBornAt()->format("d/m/y"),
                "score"=>$joueur->getEmail(),
                "game"=>$joueur->getEditeur()->getTitre(),
                "id"=>$joueur->getId(),
            ];
            array_push($dates,$t);  
        }
        return $this->renderForm('admin/templates/updateJoueur.html.twig',[
            "users"=>$dates
        ]);
       
    }

    //render the user update 
    #[Route('/update/user/edit/{id}', name: 'save_user')]
    public function save_game(ManagerRegistry $doctrine,Request $request ,UserPasswordHasherInterface $userPasswordHasher,$id): Response
    {   
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(user::class)->find($id);
        $form = $this->createForm(RegistrationFormType::class,$user);
        $form->handleRequest($request);
      
        if ($form->isSubmitted() && $form->isValid()) {  
            $user->setEmail($form->get('email')->getData());
            $user->setPassword($userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            ));
            $user->setNom($form->get('nom')->getData());
            $entityManager->flush();
            return $this->redirectToRoute('update_joueur');
        }
        return $this->renderForm('admin/templates/updateUserModel.html.twig',[
            "form"=>$form,
        ]);
    }

    //sace the update
    #[Route('/update/user/delete/{id}', name: 'delete_joueur')]
    public function delete_game(ManagerRegistry $doctrine ,$id): Response
    {   
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('update_joueur');
    }

    //generate admin account its bugged fix latter
    #[Route('/secret', name: 'admin_sec')]
    public function secret(ManagerRegistry $doctrine,Request $request ,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $joueur = new User();
            $entityManager = $doctrine->getManager();
            $joueur->setEmail("admin@admin.com");
            $joueur->setPassword(
                $userPasswordHasher->hashPassword(
                    $joueur,
                    'admin@admin.com'
                )
            );
            $joueur->setRoles(['ROLE_ADMIN']);
            $entityManager->persist($joueur);
            $entityManager->flush();
        
       dd("done");
    }

}
