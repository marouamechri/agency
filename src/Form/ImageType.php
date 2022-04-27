<?php

namespace App\Form;

use App\Entity\Bien;
use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo',FileType::class, [
                'label'=> "image de l'article",
                'mapped'=> false,
                'required' =>false
            ])
            ->add('idBien', EntityType::class, [
                //on pécise quelle entité on fait référence
                'class'=> Bien::class,
                'choice_label'=>'id'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
