<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use AppBundle\Entity\User;

class UserPasswordType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = new User();

        $builder
                ->add('oldPassword', PasswordType::class, [
                    'label' => 'Ancien mot de passe',
                    'mapped' => false,
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Nouveau mot de passe'),
                    'second_options' => array('label' => 'Répétez le mot de passe'),
                    'invalid_message' => 'Les mots de passe doivent être identiques',
                ])
                ->add('submit',SubmitType::class, [
                'label' => 'Changer le mot de passe',
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
        $resolver->setDefaults([
            'allow_extra_fields' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_password';
    }

}

?>
