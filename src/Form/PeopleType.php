<?php

namespace App\Form;

use App\Entity\Denomination;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form with all user's infos
 */
class PeopleType extends AbstractType
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
                ->add('denomination', EntityType::class, [
                    // Looks for choices from this entity
                    'class' => Denomination::class,
                    // Uses the Responsibility.label property as the visible option string
                    'choice_label' => 'label',
                    'label' => $this->translator->trans('Dénomination'),
                    'multiple' => false,
                    'expanded' => false,
                    'placeholder' => $this->translator->trans('Aucune'),
                    'required' => true,
                    'choice_attr' => function($denomination)
                    {
                        return ['data-denomination-description' => $denomination->getLabel()];
                    },
                ])
                ->add('firstname', TextType::class, [
                    'label' => $this->translator->trans('Prénom'),
                    'required' => true
                ])
                ->add('lastname', TextType::class, [
                    'label' => $this->translator->trans('Nom de famille'),
                    'required' => true
                ])
                ->add('firstContactYear', IntegerType::class, [
                    'label' => $this->translator->trans('Année du premier contact avec cette personne'),
                    'required' => true,
                ])
                ->add('isContact', CheckboxType::class, [
                    'label' => $this->translator->trans('Cette personne est un contact'),
                    'required' => false,
                ])
                ->add('needHelp', CheckboxType::class, [
                    'label' => $this->translator->trans('Cette personne est en lien avec le Pôle Social'),
                    'required' => false,
                ])
                ->add('addresses', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AddressType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'required' => false
                ])
                ->add('emailAddress', EmailType::class, [
                    'label' => $this->translator->trans('Adresse mail'),
                    'required' => false,
                    'empty_data' => '',
                ])
                ->add('isReceivingNewsletter', CheckboxType::class, [
                    'required' => false,
                    'label' => $this->translator->trans('Reçoit la newsletter')
                ])
                ->add('newsletterDematerialization', CheckboxType::class, [
                    'required' => false,
                    'label' => $this->translator->trans('Reçoit la newsletter au format dématérialisé (e-mail)')
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
                ])
                ->add('observations', TextareaType::class, [
                    'label' => $this->translator->trans('Observations'),
                    'required' => false
                ])
                ->add('sensitiveObservations', TextareaType::class, [
                    'label' => $this->translator->trans('Détails médicaux'),
                    'required' => false,
                    'empty_data' => ''
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\FormDataObject\UpdatePeopleDataFDO'
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
