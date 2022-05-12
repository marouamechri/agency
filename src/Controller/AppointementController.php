<?php

namespace App\Controller;

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
    /**
     * fonction permet au employer d'ajouter un rendez-vous
     *
     * @param Request $request
     * @param AppointementRepository $appointementRepository
     * @param BienRepository $bienRepository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/bien/maintenance/appointement/new', name: 'app_appointement_new', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
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

    /**
     * permet au utilisateur de modifier un rendez-vous
     * @param Request $request
     * @param Appointement $appointement
     * @param AppointementRepository $appointementRepository
     */
    #[Route('/bien/maintenance/appointement/{id}/edit', name: 'app_appointement_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
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

    /**
     * permet de supprimer un rendez-vous
     * @param Request $request
     * @param Appointement $appointement
     * @param AppointementRepository $appointementRepository
     */
    #[Route('/bien/maintenance/delete/{id}', name: 'app_appointement_delete', methods: ['POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function delete(Request $request, Appointement $appointement, AppointementRepository $appointementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointement->getId(), $request->request->get('_token'))) {
            $appointementRepository->remove($appointement);
        }

        return $this->redirectToRoute('maintenance', ['maintenance'=>'RDV'], Response::HTTP_SEE_OTHER);
    }
}
