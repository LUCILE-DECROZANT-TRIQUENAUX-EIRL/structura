<?php

namespace App\Form;

use App\Entity\Responsibility;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form for user's infos other than their password
 */
class UserRolesType extends AbstractType
{
    public $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('responsibilities', EntityType::class, [
                    // looks for choices from this entity
                    'class' => Responsibility::class,
                    // uses the Responsibility.label property as the visible option string
                    'choice_label' => 'label',
                    'label' => $this->translator->trans('Rôles'),
                    'multiple' => true,
                    'expanded' => true,
                    'choice_attr' => function($responsibility)
                    {
                        return ['data-responsibility-description' => $responsibility->getDescription()];
                    },
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_user';
    }

}
