<?php

namespace App\Form;

use App\Entity\Bien;
use App\Entity\Appointement;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RendezVousType extends AbstractType
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * formulaire permet de au employer d'ajouter un renvez-vous pour un de ces bien
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    
        $builder
            
            ->add('prenom',TextType::class, [
                'attr' => [
                    'placeholder'=>'Prénom'
                ]
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder'=>'Nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder'=>'Email'
                ],
                'constraints'=>[
                    new NotBlank(['message'=> 'Veuillez fournir un email valide']),
                    new Email(['message'=>'Votre email ne semble pas valide'])
                ]
            ])
            ->add('tel',TelType::class ,[
                'attr' => [
                    'placeholder'=>'Téléphone'
                ]
            ])
            ->add('date', DateTimeType::class, [
                'placeholder' => [
                    'year' => 'Anne', 'month' => 'moi', 'day' => 'jour',
                    'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Second',
                ],
            ]);

        //recuperer l'utilistauer connceter
        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException(
                'On ne peut pas recuperer les bien si on n\'a pas defeni l\employer connecter'
            );
        }
        
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {

            $form = $event->getForm();

            $form->add('titre', EntityType::class,[

                'class'=> Bien::class,
                'multiple'=>false,
                'choice_label'=>'titre',
                'query_builder' =>function(EntityRepository $er) use ($user){
                    return $er->createQueryBuilder('a')
                    ->andWhere('a.user = :bienUser')
                    ->setParameter('bienUser', $user);
                 
                }

            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointement::class,
        ]);
    }
}
