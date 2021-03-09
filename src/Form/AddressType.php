<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Address;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Form for editing an address
 */
class AddressType extends AbstractType
{
     public $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('line', TextType::class, [
                'label' => $this->translator->trans('Adresse')
            ])
            ->add('lineTwo', TextType::class, [
                'label' => $this->translator->trans('Adresse, deuxième ligne')
            ])
            ->add('postalCode', NumberType::class, [
                'label' => $this->translator->trans('Code Postal')
            ])
            ->add('city', TextType::class, [
                'label' => $this->translator->trans('Ville')
            ])
            ->add('country', TextType::class, [
                'label' => $this->translator->trans('Pays')
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_address';
    }

}

