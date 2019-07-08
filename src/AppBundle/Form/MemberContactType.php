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
 * Form with all user's infos
 */
class MemberContactType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('emailAddress', TextType::class, [
                    'label' => 'Adresse mail'
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
                    'label' => 'Téléphone fixe'
                ])
                ->add('cellPhoneNumber', TelType::class, [
                    'label' => 'Téléphone portable'
                ])
                ->add('workPhoneNumber', TelType::class, [
                    'label' => 'Téléphone de travail'
                ])
                ->add('workFaxNumber', TelType::class, [
                    'label' => 'Fax de travail'
                ])
                ->add('observations', TextType::class, [
                    'label' => 'Observations'
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
