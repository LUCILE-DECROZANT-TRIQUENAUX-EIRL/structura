<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'Nom de l\'association',
                    'required' => true
                ])
                ->add('logoFilename', TextType::class, [
                    'label' => 'Chemin d\'accès au logo de l\'association',
                ])
                ->add('treasurerSignatureFilename', TextType::class, [
                    'label' => 'Chemin d\'accès à la signature de lae trésorier.e',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
