<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form for editing a user's password
 */
class UserPasswordType extends AbstractType
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
        $user = new User();

        $builder
                ->add('oldPassword', PasswordType::class, [
                    'label' => $this->translator->trans('Ancien mot de passe'),
                    'mapped' => false,
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => array('label' => $this->translator->trans('Nouveau mot de passe')),
                    'second_options' => array('label' => $this->translator->trans('Répétez le mot de passe')),
                    'invalid_message' => $this->translator->trans('Les mots de passe doivent être identiques'),
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_password';
    }

}

