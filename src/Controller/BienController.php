<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Entity\Image;
use App\Entity\Option;
use App\Form\BienType;
use App\Entity\OptionBien;
use App\Entity\Appointement;
use App\Form\AppointementType;
use App\Repository\BienRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/')]
class BienController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(BienRepository $bienRepository): Response
    {
        return $this->render('bien/index.html.twig', [
            'biens' => $bienRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_bien_new', methods: ['GET', 'POST'])]
    public function new(Request $request,  ManagerRegistry $manager): Response
    {
        $bien = new Bien();
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on recupère les image transmises
            $images = $form->get('photo')->getData();

            //on boucle sur les image
            foreach($images as $image){
                //on génère un nouveau non du fichier 
                $fichier = md5(uniqid()) . '.' .$image->guessExtension();
                //on copie le fichier dans le dossier img/upload
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                //on stocke le nom de l'image dans BDD
                $img = new Image();
                $img->setPhoto($fichier);
                $bien->addImage($img);
            }

            
            $entityManager = $manager->getManager();
            $entityManager->persist($bien);
            //on recuper le checkbox valided
            $checked = $form->get('options')->getData();
            foreach($checked as $option){
                if($option==true){
                    $optionBien= new OptionBien();
                    $optionBien->setIdBien($bien);
                    $optionBien->setIdOption($option);
                    $entityManager->persist($optionBien);                    }
            }
            $entityManager->flush();
            $this->addFlash('success','le bien a bien été bien ajouter');
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bien/new.html.twig', [
            'bien' => $bien,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bien_show', methods: ['GET','POST'])]
    public function show(Bien $bien, ManagerRegistry $manager, Request $request): Response
    {

        $appointement = new Appointement();
        $form = $this->createForm(AppointementType::class, $appointement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $appointement = $form->getData();
            $appointement->setTitre($bien);
            $entityManager = $manager->getManager();
            $entityManager->persist($appointement);
            $entityManager->flush();

            $this->addFlash('success','la demande est envoyer');
        }

        return $this->renderForm('bien/show.html.twig', [
            'bien' => $bien,
            'form'=> $form
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bien_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bien $bien, Option $option, ManagerRegistry $manager): Response
    {
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             //on recupère les image transmises
             $images = $form->get('photo')->getData();

             //on boucle sur les image
             foreach($images as $image){
                 //on génère un nouveau non du fichier 
                 $fichier = md5(uniqid()) . '.' .$image->guessExtension();
                 //on copie le fichier dans le dossier img/upload
                 $image->move(
                     $this->getParameter('images_directory'),
                     $fichier
                 );
 
                 //on stocke le nom de l'image dans BDD
                 $img = new Image();
                 $img->setPhoto($fichier);
                 $bien->addImage($img);
                }
                $entityManager = $manager->getManager();
                $entityManager->persist($bien);

                //on recuper le checkbox valided
                $checked = $form->get('options')->getData();
                //recuperer les option existante
                $optionexisted= $bien->getOptionBiens()->getValues();
                //si notre l'option est existe on le passe a true automatiquement 
                //si on le decoche on suprime l'option de table bien
                
                foreach($checked as $option){
                    $same=false;
                    foreach($optionexisted as $optionEx){

                        if(($option==true)&&
                        ($optionEx->getIdOption()->getId() == $option->getId()) ){
                           
                                
                                $same =true;
                        }
                         
                    }
                    if(!$same){
                        $optionBien= new OptionBien();
                        $optionBien->setIdBien($bien);
                        $optionBien->setIdOption($option);
                        $entityManager->persist($optionBien);
                    }
                }
             $entityManager->flush();

            $this->addFlash('success','le bien a bien été bien modifier');
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('bien/edit.html.twig', [
            'bien' => $bien,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bien_delete', methods: ['POST'])]
    public function delete(Request $request, Bien $bien, BienRepository $bienRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bien->getId(), $request->request->get('_token'))) {
            $bienRepository->remove($bien);
        }

        return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
    }
   
    #[Route('/supprimer/image/{id}', name: 'annonces_delete_image', methods: ['DELETE'])]

    public function deleteImage(Image $image, Request $request, ManagerRegistry $manager){

        $data =Json_decode($request->getContent(), true);
        //on verifier si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){

            //on récupère le nom de l'image
            $nom = $image->getPhoto();
            //on supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);
        
            //on supprime l'entrée de la base
            $entityManager = $manager->getManager();
            $entityManager->remove($image);
            $entityManager->flush();

            //on répont en Json_decode
            return new JsonResponse(['success'=>1]);
        }else{
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }
}
