<?php

namespace App\Controller;

use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class OptionController extends AbstractController
{
    /**
     * Ajout des options
     *
     * @param Request $request
     * @param OptionRepository $optionRepository
     * @return Response
     */
    #[Route('bien/maintenance/option', name: 'app_option_new', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function new(Request $request, OptionRepository $optionRepository): Response
    {
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $optionRepository->add($option);
            return $this->redirectToRoute('maintenance', ['maintenance'=>'options'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('option/new.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }
/**
 * modifier une option
 * @param Request $request
 * @param Option $option
 * @param OptionRepository $optionRepository
 */
    #[Route('bien/maintenance/option/{id}/edit', name: 'app_option_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function edit(Request $request, Option $option, OptionRepository $optionRepository): Response
    {
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $optionRepository->add($option);
            return $this->redirectToRoute('maintenance', ['maintenance'=>'options'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('option/edit.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }
/**
 * suppriemr une option
 * @param Request $request
 * @param Option $option
 * @param OptionRepository $optionRepository
 */
    #[Route('bien/maintenance/option/{id}', name: 'app_option_delete', methods: ['POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function delete(Request $request, Option $option, OptionRepository $optionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $optionRepository->remove($option);
        }

        return $this->redirectToRoute('maintenance', ['maintenance'=>'options'], Response::HTTP_SEE_OTHER);
    }
}
