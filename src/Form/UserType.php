<?php

namespace App\Form;

use App\Entity\Responsibility;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form with all user's infos
 */
class UserType extends AbstractType
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
                ->add('username', TextType::class, [
                    'label' => $this->translator->trans('Nom d\'utilisateurice')
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => array('label' => $this->translator->trans('Mot de passe')),
                    'second_options' => array('label' => $this->translator->trans('Répétez le mot de passe')),
                    'invalid_message' => $this->translator->trans('Les mots de passe doivent être identiques'),
                ])
                ->add('responsibilities', EntityType::class, [
                    // Looks for choices from this entity
                    'class' => Responsibility::class,
                    // Uses the Responsibility.label property as the visible option string
                    'choice_label' => 'label',
                    'label' => $this->translator->trans('Rôles'),
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
                ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User',
            'empty_data' => new User(),
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
