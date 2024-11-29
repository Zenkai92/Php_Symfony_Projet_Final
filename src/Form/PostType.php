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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre',])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => 'Contenu',])
                ->add('category', EntityType::class, [
                    'class' => Category::class,  
                    'choice_label' => 'name',  
                    'placeholder' => 'Sélectionner une catégorie',  
                    'required' => true, 
                    'label' => 'Catégorie', 
                ])

            ->add('picture', TextType::class, [
                'required' => true,
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