<?php
/*Ici on retrouve les éléments qui forme les formulaires pour la création et la modification de posts*/

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('category', EntityType::class, [
            'class' => Category::class,  
            'choice_label' => 'name',  
            'placeholder' => 'Sélectionner une catégorie',  
            'required' => false, 
            'label' => 'Catégorie', 
        ])
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Titre',])
            ->add('content', TextType::class, [
                'required' => false,
                'label' => 'Contenu',])
            ->add('picture', TextType::class, [
                'required' => false,
                'label' => 'URL de l\'image',
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}

?>