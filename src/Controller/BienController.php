<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Entity\Image;
use App\Form\BienType;
use App\Data\SearchData;
use App\Entity\OptionBien;
use App\Entity\Appointement;
use App\Form\AppointementType;
use App\Repository\BienRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AppointementRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\SearchFormType as SearchFormType;
use App\Repository\OptionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BienController extends AbstractController
{

    /**
     * index de mon site
     *
     * @param Request $request
     * @param BienRepository $bienRepository
     * @return Response
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, BienRepository $bienRepository): Response
    {
        //pagination
        $limit = 4;
        //on recupere le num de la page
        $page = (int)$request->query->get("page", 1);
        // recupere les bien a vendre et à louer
        $annonces = $bienRepository->getPaginationAnnonces($page, $limit);

        // on recupere le nombre total du bien
        $total = $bienRepository->getCountTotalBien();
        //recuperer le trie du href
        (string)$trie = $request->query->get("trie");
        if ($trie) {
            $annonces = $bienRepository->getlisttrie((string)$trie,$page, $limit);
            $total = $bienRepository->getCountTotalBienTrier($trie);
        }
        return $this->render('bien/index.html.twig', [
            'biens' => $annonces,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
            'trie' => $trie
        ]);
    }
    /**
     * maintenance de site
     *
     * @param Request $request
     * @param BienRepository $bienRepository
     * @param AppointementRepository $appointementRepository
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/bien/maintenance', name: 'maintenance', methods: ['GET'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function maintenance(Request $request, OptionRepository $optionRepository, BienRepository $bienRepository, AppointementRepository $appointementRepository, UserRepository $userRepository, UserInterface $user): Response
    {
        //recuperer le get de maontenance
        //par défaut maintenance = bien
        $maintenance = $request->query->get("maintenance");
        if (!$maintenance) {
            $maintenance = 'bien';
        }

        //recupére get archive
        $archive = $request->query->get("archive");
        if (!$archive) {
            $archive = 'false';
        }
        //la liste des rendez-vous par employer
        $appointements = $appointementRepository->getAppointement($user);

        //liste des bien par employer
        $biens = $bienRepository->getannacesUser($user);

        //liste des Options
        $options = $optionRepository->findAll();
        
        //la liste des employers
        $users = $userRepository->findAll();

        //recuperer les bien de l'archive (bien vendu ou louer)
        $bienArchiveUser = $bienRepository->getannanceArchiverUser($user);
       
        //recuperer tout les bien de l'archive
        $bienArchive = $bienRepository->getAllBienArchive();

        return $this->render('bien/maintenance.html.twig', [
            //bien de l'employer disponible(A vendre ou A louer)de l'utalisateur connecetre
            'biensDispoUser' => $biens,
            //lout les bien disponible
            'AllBiensDispo' => $bienRepository->getAllBienDispo(),
            //get de maintenence
            'maintenance' => $maintenance,
            //rendez-vous par employer
            'appointements' => $appointements,
            //liste de tout les rendez-vous
            'toutsAppointement' => $appointementRepository->findAll(),
            //emplyer conneceter
            'users' => $users,
            //liste des options de bien
            'options' => $options,
            //liste des biens de l'archive par user
            'bienArchiveUser'=> $bienArchiveUser,
            //liste de tout les biens de l'archive 
            'bienArchive' => $bienArchive,
            //get archive
            'archive'=>$archive
        ]);
    }
    /**
     * ajoute d'un bien
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ManagerRegistry $manager
     * @param UserInterface $userconnect
     * @return Response
     */
    #[Route("/bien/maintenance/bien/new", name: 'app_bien_new', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function new(Request $request, UserRepository $userRepository,  ManagerRegistry $manager, UserInterface $userconnect): Response
    {
        $bien = new Bien();
        // $allUser = $userRepository->findAll();
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on recupère les image transmises
            $images = $form->get('photo')->getData();

            //on boucle sur les image
            foreach ($images as $image) {
                //on génère un nouveau non du fichier 
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                //on copie le fichier dans le dossier img/upload
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                //on stocke le nom de l'image dans BDD
                $img = new Image();
                //on cree une image dans mon table image
                $img->setPhoto($fichier);
                //on rajoute l'image à mon bien
                $bien->addImage($img);
            }

            $entityManager = $manager->getManager();
            $entityManager->persist($bien);
            //on recuper le checkbox valider
            $checked = $form->get('options')->getData();
            foreach ($checked as $option) {
                if ($option == true) {
                    $optionBien = new OptionBien();
                    $optionBien->setIdBien($bien);
                    $optionBien->setIdOption($option);
                    $entityManager->persist($optionBien);
                }
            }
            //si l'utilisateur connecter n'est pas un admin l'user de bien prendra automatiquement l'utilisateur connecté
            if (!$this->isGranted('ROLE_ADMIN')) {
                $bien->setUser($userconnect);
            }
            //enregister dans ma bdd
            $entityManager->flush();
            $this->addFlash('success', 'le bien a bien été bien ajouter');
            return $this->redirectToRoute('maintenance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bien/new.html.twig', [
            'bien' => $bien,
            'form' => $form,
            'userConnecter' =>$userconnect,
        ]);
    }

    /**
     * afficher les detailles des bien et permet au visiteur de site de prendre un rendez-vous
     * @param Bien $bien
     * @param ManagerRegistry $manager
     * @param Request $request
     */
    #[Route('bien/{id}/show', name: 'app_bien_show', methods: ['GET', 'POST'])]
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

            $this->addFlash('success', 'la demande est envoyer');
        }

        return $this->renderForm('bien/show.html.twig', [
            'bien' => $bien,
            'form' => $form,
        ]);
    }

    /**
     * fonction permet de modifier un bien 
     * @param Request $request
     * @param Bien $bien
     * @param ManagerRegistry $manager
     * @param UserInterface $userconnect
     */
    #[Route('bien/maintenance/bien/{id}/edit', name: 'app_bien_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function edit(Request $request, Bien $bien, ManagerRegistry $manager, UserInterface $userconnect): Response
    {
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);
        //recuperer les option existante
        $optionexisted = $bien->getOptionBiens()->getValues();
        if ($form->isSubmitted() && $form->isValid()) {
            //on recupère les image transmises
            $images = $form->get('photo')->getData();

            //on boucle sur les image
            foreach ($images as $image) {
                //on génère un nouveau non du fichier 
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
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
            //si notre l'option est existe on le passe a true automatiquement 
            //si on le decoche on suprime l'option de table bien

            foreach ($checked as $option) {

                $same = false;

                foreach ($optionexisted as $optionEx) {

                    if (($option == true) &&
                        ($optionEx->getIdOption()->getId() == $option->getId())
                    ) {

                        $same = true;
                    }
                }
                if (!$same) {
                    $optionBien = new OptionBien();
                    $optionBien->setIdBien($bien);
                    $optionBien->setIdOption($option);
                    $entityManager->persist($optionBien);
                }
            }
            //si l'utilisateur connecter n'est pas un admin l'user de bien prendra automatiquement l'utilisateur connecté
            if (!$this->isGranted('ROLE_ADMIN')) {
                $bien->setUser($userconnect);
            }
            $entityManager->flush();

            $this->addFlash('success', 'le bien a bien été bien modifier');
            return $this->redirectToRoute('maintenance', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('bien/edit.html.twig', [
            'bien' => $bien,
            'form' => $form,
            'optionchcked' => $optionexisted,
            'userConnecter' =>$userconnect,

        ]);
    }
    /**
     * fonction permet de supprimer un bien avec ces image concerner
     * @param Request $request
     * @param Bien $bien
     * @param BienRepository $bienRepository
     */
    #[Route('/maintenance/bien/{id}', name: 'app_bien_delete', methods: ['POST'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function delete(Request $request, Bien $bien, BienRepository $bienRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bien->getId(), $request->request->get('_token'))) {
            $images = $bien->getImages();
            foreach($images as $image){
                //on récupère le nom de l'image
                $nom = $image->getPhoto();
                //on supprime le fichier
                unlink($this->getParameter('images_directory') . '/' . $nom);
            }
            $bienRepository->remove($bien);
        }

        return $this->redirectToRoute('maintenance', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * fonction permet de supprimer une image
     * @param Image $image
     * @param Request $request
     * @param ManagerRegistry $manager
     */
    #[Route('/supprimer/image/{id}', name: 'annonces_delete_image', methods: ['DELETE'])]
    #[IsGranted(data: 'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function deleteImage(Image $image, Request $request, ManagerRegistry $manager)
    {

        $data = Json_decode($request->getContent(), true);
        //on verifier si le token est valide
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {

            //on récupère le nom de l'image
            $nom = $image->getPhoto();
            //on supprime le fichier
            unlink($this->getParameter('images_directory') . '/' . $nom);

            //on supprime l'entrée de la base
            $entityManager = $manager->getManager();
            $entityManager->remove($image);
            $entityManager->flush();

            //on répont en Json_decode
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }

    /**
     * fonction permet afficher les bien avec un filtre
     *
     * @param Request $request
     * @param BienRepository $repository
     * @return Response
     */
    #[Route('Bien/search', name: 'search', methods: ['GET', 'POST'])]
    public function search(Request $request, BienRepository $repository): Response
    {
        //recupére la page
        $page = (int) $request->query->get("page", 1);
        //pagination
        $limit = 5;
        //on recupere les bien de la page
        $annonces = $repository->getPaginationAnnonces($page, $limit);
        // on recupere le nombre total du bien
        $total = $repository->getCountTotalBien();

        $data = new SearchData();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

       //recuperer le orderPieces du href
       (string)$orderPieces = $request->query->get("orderPieces");
       //recuperer le orderSurfaces du href
       (string)$orderSurfaces = $request->query->get("orderSurfaces");
       //recuperer le orderPrix du href
       (string)$orderPrix = $request->query->get("orderPrix");

        if ($form->isSubmitted() && $form->isValid()) {
            //recuperer les bien filtrer par page
            $annonces = $repository->getListefiltre($data, $page, $limit);
            $total = $repository->getCoutListefiltre($data);
        } elseif ($orderPieces) {
            // on retourne les biens ordonner par le nombre de piéces (asc ou desc)
            $annonces = $repository->orderPieces($orderPieces,$page, $limit);
        } elseif ($orderSurfaces) {
            // on retourne les biens ordonner par la surface(asc ou desc)
            $annonces = $repository->orderSurfaces($orderSurfaces,$page, $limit);
        } elseif ($orderPrix) {
            // on retourne les biens ordonner par le prix(asc ou desc)
            $annonces = $repository->orderPrix($orderPrix,$page, $limit);
        }

        //recuperer tous mes produit filtree
        return $this->renderForm('filter/search.html.twig', [
            //form searche
            'formfiltre' => $form,
            //annonce à afficher
            'annonces' => $annonces,
            //limite de annance par page
            'limit' => $limit,
            //nombre total des biens à afficher
            'total' => $total,
            //get nombre de  page
            'page' => $page,
            //order nombre des piéces
            'orderPieces'=>$orderPieces,
            //order surface
            'orderSurfaces'=> $orderSurfaces,
            //order prix
            'orderPrix'=>$orderPrix
        ]);
    }
}
