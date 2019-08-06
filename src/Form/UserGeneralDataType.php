<?php

namespace App\Form;

use App\Entity\Responsibility;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for user's infos other than their password
 */
class UserGeneralDataType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', TextType::class, [
                    'label' => 'Nom d\'utilisateurice'
                ])
                ->add('responsibilities', EntityType::class, [
                    // looks for choices from this entity
                    'class' => Responsibility::class,
                    // uses the Responsibility.label property as the visible option string
                    'choice_label' => 'label',
                    'label' => 'Rôles',
                    'multiple' => true,
                    'expanded' => true,
                    'choice_attr' => function($responsibility)
                    {
                        return [
                            'data-responsibility-description' => $responsibility->getDescription(),
                            'data-responsibility-automatically-managed' => $responsibility->isAutomatic(),
                        ];
                    },
                ])
                ->add('submit',SubmitType::class, [
                'label' => 'Changer les informations',
                'attr' => [
                    'class' => 'btn btn-primary float-right'
                    ]
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User'
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
