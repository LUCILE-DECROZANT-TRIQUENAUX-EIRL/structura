<?php

namespace App\Form;

use App\Entity\Denomination;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form to edit a user personal infos
 */
class PeoplePersonalType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('denomination', EntityType::class, [
                    // Looks for choices from this entity
                    'class' => Denomination::class,
                    // Uses the Responsibility.label property as the visible option string
                    'choice_label' => 'label',
                    'label' => 'Dénomination',
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => 'Aucune',
                    'required' => true,
                    'choice_attr' => function($denomination)
                    {
                        return ['data-denomination-description' => $denomination->getLabel()];
                    },
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom',
                    'required' => true
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'Nom de famille',
                    'required' => true
                ])
                ->add('observations', TextareaType::class, [
                    'label' => 'Observations',
                    'required' => false
                ])
                ->add('sensitiveObservations', TextareaType::class, [
                    'label' => 'Détails médicaux',
                    'required' => false
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\People'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user';
    }

}
