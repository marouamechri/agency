<?php

namespace App\Form;

use App\Entity\Bien;
use App\Entity\User;
use App\Entity\Option;
use App\Entity\OptionBien;
use App\Repository\OptionRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class BienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('titre',TextType::class,[
                'label'=> 'Le Titre de bien',
            ] )
            ->add('nbPiece', NumberType::class, [
                'label'=> 'nombre de piece'
            ])
            ->add('surface',  NumberType::class, [
                'label'=> 'Suraface de bien'
            ])
            ->add('prix',  NumberType::class, [
                'label'=> 'Prix'
            ])
            ->add('localisation',TextType::class,[
                'label'=> 'Localisation'
            ])
            ->add('type', TextType::class,[
                'label'=> 'Type (Appartement ou Maison)'
            ])
            ->add('etage',  NumberType::class, [
                'label'=> 'Etage'
            ])
            ->add('transactionType', TextType::class,[
                'label'=> 'Type de transaction( A vendre, Alouer, Vendu, Louer) '
            ])
            ->add('description', CKEditorType::class, [
                'label'=> 'Description du bien'
            ])
            ->add('dateConstruction', DateType::class, [
                'label'=> 'date de creation'
            ])
            //ajouter le champ image dans la formulaire
            ->add('photo', FileType::class, [
                'label'=>'Ajouter les images',
                'multiple'=> true,
                'mapped'=>false,
                'required'=>false
            ] )
            //ajouter liste de options
            ->add('options', EntityType::class, [
                //'attr' => array('checked'   => 'checked'),
                'class'=> Option::class,
                'choice_label'=>'designation',
                'expanded'=>true,
                'multiple'=>true,
                'mapped'=>false
            ])
            //si l'utilistateur est un admin il peut choisire le employer qui s'occupe du bien
            ->add('user', EntityType::class, [
                'class'=> User::class,
                'multiple'=>false,
                'choice_label'=>'nom',
                'mapped'=>true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bien::class,
        ]);
    }
}
