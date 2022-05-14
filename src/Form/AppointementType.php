<?php

namespace App\Form;

use App\Entity\Appointement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AppointementType extends AbstractType
{
    /**
     * formulaire permet au visiteur de site de prendre un rendez-vous
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
                    'year' => 'Année', 'month' => 'Mois', 'day' => 'Jour',
                    'hour' => 'Heure', 'minute' => 'Minute', 'second' => 'Seconde',
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointement::class,
        ]);
    }
}
