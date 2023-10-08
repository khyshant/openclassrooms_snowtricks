<?php

namespace App\Form;

use App\Entity\GroupTrick;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('metaTitle', TextType::class)
            ->add('metaDescription', TextareaType::class)
            ->add('valid', ChoiceType::class, [
                'choices' => array(
                    'Oui' => '1',
                    'Non' => '0'
                ),
                'label' => 'Publié',
                'required' => true,
            ])
            ->add('GroupTrick', EntityType::class, [
                'mapped' =>true,
                'class' => GroupTrick::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,])
            ->add("images", CollectionType::class, [
                "entry_type" => ImageType::class,
                "allow_add" => true,
                "allow_delete" => true,
                "by_reference" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
