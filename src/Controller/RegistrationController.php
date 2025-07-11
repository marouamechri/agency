<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditeUserType;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * permet au administrateur d'enregistrer un user
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * 
     */
    #[Route('bien/maintenance/user/register', name: 'app_register')]
    #[IsGranted(data: 'ROLE_ADMIN', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('maroua@agence.com', 'Agence'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * fonction permet de modifier un utolisateur enregistrer
     * *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param UserRepository $userRepository
     * @param User $user
     * 
     * 
     */
    #[Route('bien/maintenance/user/register/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_ADMIN', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function edit(Request $request, User $user, UserRepository $userRepository,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(EditeUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             
                $userRepository->add($user);

               
           return $this->redirectToRoute('maintenance', ['maintenance'=>'user'], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    
    /**
     * fonction permet de supprimer un utilisateur
     * * *
     * @param Request $request
     * @param UserInterface $admin
     * @param UserRepository $userRepository
     * @param User $user
     * 
     * 
     */
    #[Route('bien/maintenance/user/{id}/delete', name: "app_user_delete",methods: ['POST'], requirements: ['id' => "[0-9]+"])]
    #[IsGranted(data: 'ROLE_ADMIN', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function delete(Request $request, User $user, UserRepository $userRepository, UserInterface $admin)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            
        
            //on recupere tout les biens qui sont attachés au user à supprimer
           $allBiensUser = $user->getBiens();
           //on les parcours 
           foreach( $allBiensUser as $unBien){
                //on les attache avec l'admin
                $unBien->setUser($admin);
           }
           
            $userRepository->remove($user);
        }

        $this->addFlash("success", "L'utilisateur a été supprimé avec succés");
        return $this->redirectToRoute('maintenance', ['maintenance'=>'user'], Response::HTTP_SEE_OTHER);
       
    }

    /**
     * fonction permet de verifier le email
     * 
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param UserRepository $userRepository
     *
     */

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
