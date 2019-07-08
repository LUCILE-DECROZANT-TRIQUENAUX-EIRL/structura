<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form to edit a user personal infos
 */
class MemberPersonalType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('denomination', EntityType::class, [
                    // Looks for choices from this entity
                    'class' => 'AppBundle:Denomination',
                    // Uses the Responsibility.label property as the visible option string
                    'choice_label' => 'label',
                    'label' => 'Dénomination',
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => 'Aucune',
                    'choice_attr' => function($denomination)
                    {
                        return ['data-denomination-description' => $denomination->getLabel()];
                    },
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom'
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'Nom de famille'
                ])
                ->add('submit',SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-outline-primary float-right'
                    ]
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\People'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }

}
