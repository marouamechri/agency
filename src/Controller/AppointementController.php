<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Entity\Appointement;
use App\Form\AppointementType;
use App\Repository\AppointementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppointementController extends AbstractController
{
    #[Route("/bien/maintenancepage<\d+>?maintenance='appointement'", name: 'app_appointement_index', methods: ['GET'])]
    public function index(AppointementRepository $appointementRepository): Response
    {
        return $this->render('appointement/index.html.twig', [
            'appointements' => $appointementRepository->findAll(),
        ]);
    }

    #[Route('/bien/maintenance?maintenance=RDV/new', name: 'app_appointement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AppointementRepository $appointementRepository): Response
    {
        $appointement = new Appointement();
        $form = $this->createForm(AppointementType::class, $appointement);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $appointementRepository->add($appointement);
            return $this->redirectToRoute('app_appointement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('appointement/new.html.twig', [
            'appointement' => $appointement,
            'form' => $form,
        ]);
    }

    #[Route('/bien/maintenance?maintenance=RDV/{id}', name: 'app_appointement_show', methods: ['GET'])]
    public function show(Appointement $appointement): Response
    {
        return $this->render('appointement/show.html.twig', [
            'appointement' => $appointement,
        ]);
    }

    #[Route('/bien/maintenance?maintenance=RDV/{id}/edit', name: 'app_appointement_edit', methods: ['GET', 'POST'])]
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

    #[Route('/bien/maintenance?maintenance=RDV/{id}', name: 'app_appointement_delete', methods: ['POST'])]
    public function delete(Request $request, Appointement $appointement, AppointementRepository $appointementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointement->getId(), $request->request->get('_token'))) {
            $appointementRepository->remove($appointement);
        }

        return $this->redirectToRoute('app_appointement_index', [], Response::HTTP_SEE_OTHER);
    }
}
