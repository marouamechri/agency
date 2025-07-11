<?php 
namespace App\Form;
use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class SearchFormType extends AbstractType
{
    /**
     * formulaire qui traite le filter sur la liste de bien
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbpiecemin', NumberType::class, [
                'label' => 'Pièces min',
                'required' => false
            ])
            ->add('nbpiecemax', NumberType::class, [
                'label' => 'Piéces max',
                'required' => false
            ])

            ->add('surfacemin', NumberType::class, [
                'label' => 'Surface min',
                'required' => false
            ])
            ->add('surfacemax', NumberType::class, [
                'label' => 'Surface max',
                'required' => false
                
            ])
            ->add('prixmin', NumberType::class, [
                'label' => 'Prix min',
                'required' => false
                
            ])
            ->add('prixmax', NumberType::class, [
                'label' => 'Prix max',
                'required' => false
            ]);
  }  

  public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
        
    }

    // public function getBlockPreFix(){
    //     return '';
    // }

}