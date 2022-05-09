<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: 'bien/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {

            return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: 'bien/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path:'/bien/pass-oublier', name:'app_pw_oublier')]
    public function forgottenpss(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGeneratorInterface)
    {
        //on crée la formulaire
        $form = $this->createForm(ResetPassType::class);
        //on traite le formulaire
        $form->handleRequest($request);
        //si la formulaire est valide
        if($form->isSubmitted() && $form->isValid()){
            //on recupére les $donnees
            $donnees = $form->getData();
            //on cherche si un utilisateur a cet email
            $user = $userRepository->finOneByEmail($donnees['email']);

            //si l'utilisateur n'existe pas 
            if(!$user){
                //on envoi un message flash
                $this->addFlash('danger', 'cette adresse n\'existe pas');
                $this->redirectToRoute('app_login');
            }
            //on génére un Token
            $token =$tokenGeneratorInterface->generateToken();

            try{
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManger();
                $entityManager->persist($user);
                $entityManager->flush();
            }catch(\Exception $e){
                $this->addFlash('warning', 'une erreur est survenu: '.$e->getMessage());
                return $this->redirectToRoute('app_login');
            }
            //on g"n"re l'url de renitialisation de mot de passe
            $url=$this->generateUrl('app_restet_pw',['token'=>$token]);
            //on envoie le message
            $message =(new \Swift_Message('Mot de passe oublier'))
            ->setForm('maroua@agence.com')
            ->setTo($user->getEmail())
            ->setBody(
                "<p>Bonjour,</p><p>une demande de rénitialisation de mot de passe a été effectuée pour le site Agency.Veuillez cliquer sue le lien suivant: "
                .$url."</p>", 'text/html'
            );
            //on envoie l'email
            $mailer->send($message);
            //on cree le message flash 
            $this->addFlash('message', 'un e-mail de rénitialisation de mot de passe vous a été envoyé');
            return $this->redirectToRoute('app_login');
        }
        //on envoie vers la page de demande de l'email
        return $this->render('security/forgotten_password.html.twig',['emailForm'=>$form->createView()]);
    }
}
