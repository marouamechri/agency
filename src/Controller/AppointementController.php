<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Entity\Appointement;
use App\Form\AppointementType;
use App\Form\RendezVousType;
use App\Repository\AppointementRepository;
use App\Repository\BienRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppointementController extends AbstractController
{
    
    #[Route('/bien/maintenance/appointement/new', name: 'app_appointement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AppointementRepository $appointementRepository, BienRepository $bienRepository,UserInterface $user ): Response
    {
    
        
        $appointement = new Appointement();
        $form = $this->createForm(RendezVousType::class, $appointement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointement = $form->getData();
            $appointementRepository->add($appointement);
            return $this->redirectToRoute('maintenance', ['maintenance'=>'RDV'], Response::HTTP_SEE_OTHER);
            $this->addFlash('success', 'la demande est envoyer');
        }
       
        return $this->renderForm('appointement/new.html.twig', [
            'appointement' => $appointement,
            'form' => $form
        ]);
    
    }

    // #[Route('/bien/maintenance/appointement/{id}/show', name: 'app_appointement_show', methods: ['GET'])]
    // public function show(Appointement $appointement): Response
    // {
    //     return $this->render('appointement/show.html.twig', [
    //         'appointement' => $appointement,
            
    //     ]);
    // }

    #[Route('/bien/maintenance/appointement/{id}/edit', name: 'app_appointement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Appointement $appointement, AppointementRepository $appointementRepository): Response
    {
        $form = $this->createForm(AppointementType::class, $appointement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointementRepository->add($appointement);
            return $this->redirectToRoute('maintenance',['maintenance'=>'appointement'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('appointement/edit.html.twig', [
            'appointement' => $appointement,
            'form' => $form,
        ]);
    }

    #[Route('/bien/maintenance/delete/{id}', name: 'app_appointement_delete', methods: ['POST'])]
    public function delete(Request $request, Appointement $appointement, AppointementRepository $appointementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointement->getId(), $request->request->get('_token'))) {
            $appointementRepository->remove($appointement);
        }

        return $this->redirectToRoute('maintenance', ['maintenance'=>'RDV'], Response::HTTP_SEE_OTHER);
    }
}
