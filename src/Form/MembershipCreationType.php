<?php

namespace App\Form;

use App\Entity\People;
use App\Entity\PaymentType;
use App\Entity\MembershipType;
use App\Repository\PeopleRepository;

use Symfony\Component\Form\AbstractType;
use App\FormDataObject\CreateMembershipFDO;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembershipCreationType extends AbstractType
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
        $builder->add('membershipType', EntityType::class, [
            'class' => MembershipType::class,
            'choice_label' => 'label',
            'multiple' => false,
            'expanded' => false,
            'label' => $this->translator->trans('Enregister une adhésion de type'),
            'attr' => [
                'autocomplete' => 'off',
            ],
            'placeholder' => $this->translator->trans('Sélectionnez un type d\'adhésion'),
        ]);

        $builder->add('membershipDate_start', DateType::class, [
            'label' => $this->translator->trans('L\'adhésion est valable du'),
            'widget' => 'single_text',
        ]);

        $builder->add('membershipDate_end', DateType::class, [
            'label' => $this->translator->trans('Au'),
            'widget' => 'single_text',
        ]);

        $builder->add('membershipFiscal_year', IntegerType::class, [
            'label' => $this->translator->trans('Pour l\'année fiscale'),
        ]);

        $builder->add('membershipComment', TextareaType::class, [
            'label' => $this->translator->trans('Commentaire sur l\'adhésion'),
            'required' => false,
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);

        $builder->add('paymentType', EntityType::class, [
            'label' => $this->translator->trans('Via'),
            'choice_label' => 'label',
            'class' => PaymentType::class,
            'multiple' => false,
            'expanded' => false,
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);

        $builder->add('membershipAmount', MoneyType::class, [
            'label' => $this->translator->trans('Soit une adhésion d\'un montant de'),
            'attr' => [
                'readonly' => 'readonly',
            ],
        ]);

        $builder->add('donationAmount', MoneyType::class, [
            'label' => $this->translator->trans('Et un don d\'un montant de'),
            'attr' => [
                'readonly' => 'readonly'
            ],
            'required' => false,
        ]);

        $builder->add('paymentAmount', MoneyType::class, [
            'label' => $this->translator->trans('Le règlement est de'),
        ]);

        $builder->add('paymentDate_received', DateType::class, [
            'label' => $this->translator->trans('Le'),
            'widget' => 'single_text',
            'required' => false,
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);

        $builder->add('paymentDate_cashed', DateType::class, [
            'label' => $this->translator->trans('Encaissés le'),
            'widget' => 'single_text',
            'required' => false,
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);

        $builder->add('payer', EntityType::class, [
            'label' => $this->translator->trans('Effectué par'),
            'class' => People::class,
            'attr' => [
                'autocomplete' => 'off',
            ],
            'choices' => $options['peopleWithNoActiveMembership'],
            'choice_label' => function (People $people) {
                $peopleDenomination = ($people->getDenomination() != null) ? $people->getDenomination()->getLabel() . ' ' : '';
                return $peopleDenomination . $people->getFirstName() . ' ' . strtoupper($people->getLastName());
            },
            'multiple' => false,
            'expanded' => false,
        ]);

        $builder->add('members', EntityType::class, [
            'class' => People::class,
            'choices' => $options['peopleWithNoActiveMembership'],
            'choice_label' => function (People $people) {
                return $people->getFirstName() . ' ' . $people->getLastName();
            },
            'multiple' => true,
            'expanded' => true,
        ]);

        $builder->add('newMember', EntityType::class, [
            'label' => $this->translator->trans('Choisir les personnes concernées'),
            'class' => People::class,
            'choices' => $options['peopleWithNoActiveMembership'],
            'choice_label' => function (People $people) {
                $peopleDenomination = ($people->getDenomination() != null) ? $people->getDenomination()->getLabel() . ' ' : '';
                return $peopleDenomination . $people->getFirstName() . ' ' . strtoupper($people->getLastName());
            },
            'multiple' => false,
            'expanded' => false,
            'attr' => [
                'class' => 'selectpicker',
                'data-live-search' => 'true',
                'data-live-search-normalize' => 'true',
                'data-live-search-placeholder' => $this->translator->trans('Rechercher...'),
                'data-size' => '6',
                'title' => $this->translator->trans('Sélectionnez une personne pour l\'ajouter'),
                'data-style-base' => 'custom-form-dropdown',
                'data-style' => 'btn',
            ],
            'required' => false,
            'help' => $this->translator->trans('adhérent·e·s maximum pour une adhésion de type')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateMembershipFDO::class,
            'peopleWithNoActiveMembership' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_membership_create';
    }
}