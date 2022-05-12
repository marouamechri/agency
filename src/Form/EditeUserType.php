<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditeUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'attr' => [
                    'placeholder'=>'nom'
                ]
            ])
            ->add('prenom', TextType::class,[
                'attr' => [
                    'placeholder'=>'Prénom'
                ]
            ])
            ->add('email', EmailType::class,[
                'constraints'=>[
                    new NotBlank([
                        'message' =>'Merci de saisire une adress email'
                    ])
                    ],
                    'required'=>true,
                    'attr'=> [
                        'class'=>'form-control',
                        'placeholder'=>'Email'
                    ]
            ])
            ->add('roles', ChoiceType::class,[
                'choices'=>[
                    'Employer'=>'ROLE_USER',
                    'Administrateur'=>'ROLE_ADMIN'
                ],
                'expanded'=>true,
                'multiple'=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
