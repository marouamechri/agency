<?php

namespace App\Form;

use App\Entity\Bien;
use App\Entity\Appointement;
use Symfony\Component\Mime\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AppointementType extends AbstractType
{
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
