<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', TextType::class, array(
                    'label' => 'Nom d\'utilisateurice'
                ))
                /*->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Répétez le mot de passe'),
                ))*/
                ->add('responsabilities', EntityType::class, array(
                    // looks for choices from this entity
                    'class' => 'AppBundle:Responsability',
                    // uses the Responsability.label property as the visible option string
                    'choice_label' => 'label',
                    'label' => 'Rôles',
                    'multiple' => true,
                    'expanded' => true,
                    'choice_attr' => function($responsability)
                    {
                        return ['data-responsability-description' => $responsability->getDescription()];
                    },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
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
