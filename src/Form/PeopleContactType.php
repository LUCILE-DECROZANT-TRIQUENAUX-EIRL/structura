<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form to edit a person contact infos
 */
class PeopleContactType extends AbstractType
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
                ->add('emailAddress', EmailType::class, [
                    'label' => $this->translator->trans('Adresse mail'),
                    'required' => false
                ])
                ->add('isReceivingNewsletter', CheckboxType::class, [
                    'required' => false,
                    'label' => $this->translator->trans('Reçoit la newsletter')
                ])
                ->add('newsletterDematerialization', CheckboxType::class, [
                    'required' => false,
                    'label' => $this->translator->trans('Reçoit la newsletter au format dématérialisé')
                ])
                ->add('addresses', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AddressType::class,
                    'entry_options' => ['label' => false],
                    'required' => false
                ])
                ->add('homePhoneNumber', TelType::class, [
                    'label' => $this->translator->trans('Téléphone fixe'),
                    'help' => $this->translator->trans('Les numéros doivent commencer par 01, 02, 03, 04, 05, 08 ou 09 et ne comporter que des chiffres'),
                    'required' => false
                ])
                ->add('cellPhoneNumber', TelType::class, [
                    'label' => $this->translator->trans('Téléphone portable'),
                    'help' => $this->translator->trans('Les numéros doivent commencer par 06 ou 07 et ne comporter que des chiffres'),
                    'required' => false
                ])
                ->add('workPhoneNumber', TelType::class, [
                    'label' => $this->translator->trans('Téléphone de travail'),
                    'help' => $this->translator->trans('Les numéros doivent commencer par 0 et ne comporter que des chiffres'),
                    'required' => false
                ])
                ->add('workFaxNumber', TelType::class, [
                    'label' => $this->translator->trans('Fax de travail'),
                    'help' => $this->translator->trans('Les numéros doivent commencer par 0 et ne comporter que des chiffres'),
                    'required' => false
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\People'
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
