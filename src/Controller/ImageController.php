<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

 #[Route('/image')]
class ImageController extends AbstractController
{
    // #[Route('/', name: 'app_image_index', methods: ['GET'])]
    // public function index(ImageRepository $imageRepository): Response
    // {
    //     return $this->render('image/index.html.twig', [
    //         'images' => $imageRepository->findAll(),
    //     ]);
    // }
    /**
     * function permet de ajouter une liste  des image
     *
     * @param Request $request
     * @param ImageRepository $imageRepository
     * @return Response
     */
    #[Route('/new', name: 'app_image_new', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function new(Request $request, ImageRepository $imageRepository): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image= $form->getData();
            //on recupére les information de l'image reçu à traversle form
            $image = $form->get('photo')->getData();
            if($image){
                //on genere un nouveau nom de fichier pour eviter les conflits entre les fichier
                $imageName = md5(uniqid()). "." .$image->guessExtension();
                //on deplace le fichier dans le dossier définit
                $image->move($this->getParameter('upload_dir'), $imageName);
                //on enregistre en BDD le nouveau non de fichier$
                $image->setPhoto((string)$imageName);
            }
            $imageRepository->add($image);
            $this->addFlash("success", "L'article a été ajouter avec succés");
            return $this->redirectToRoute('app_image_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('image/new.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }
}
