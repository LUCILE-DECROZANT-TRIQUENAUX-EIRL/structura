<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form with all user's infos
 */
class MemberType extends AbstractType
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
                ->add('addresses', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AddressType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'required' => false
                ])
                ->add('emailAddress', TextType::class, [
                    'label' => 'Adresse mail',
                    'required' => false
                ])
                ->add('isReceivingNewsletter', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Reçoit la newsletter'
                ])
                ->add('newsletterDematerialization', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Reçoit la newsletter au format dématérialisé'
                ])
                ->add('homePhoneNumber', TelType::class, [
                    'label' => 'Téléphone fixe',
                    'required' => false
                ])
                ->add('cellPhoneNumber', TelType::class, [
                    'label' => 'Téléphone portable',
                    'required' => false
                ])
                ->add('workPhoneNumber', TelType::class, [
                    'label' => 'Téléphone de travail',
                    'required' => false
                ])
                ->add('workFaxNumber', TelType::class, [
                    'label' => 'Fax de travail',
                    'required' => false
                ])
                ->add('observations', TextareaType::class, [
                    'label' => 'Observations',
                    'required' => false
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
