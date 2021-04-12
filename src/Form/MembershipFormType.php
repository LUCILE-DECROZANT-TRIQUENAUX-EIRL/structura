<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\MembershipType;
use App\Entity\PaymentType;
use App\Entity\People;
use App\Repository\PeopleRepository;

use Symfony\Component\Form\AbstractType;
use App\FormDataObject\UpdateMembershipFDO;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembershipFormType extends AbstractType
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
            'required' => true,
        ]);

        $builder->add('membershipDate_start', DateType::class, [
            'label' => $this->translator->trans('L\'adhésion est valable du'),
            'widget' => 'single_text',
            'required' => true,
        ]);

        $builder->add('membershipDate_end', DateType::class, [
            'label' => $this->translator->trans('Au'),
            'widget' => 'single_text',
            'required' => true,
        ]);

        $builder->add('membershipFiscal_year', IntegerType::class, [
            'label' => $this->translator->trans('Pour l\'année fiscale'),
            'required' => true,
        ]);

        $builder->add('membershipComment', TextareaType::class, [
            'label' => $this->translator->trans('Commentaire sur l\'adhésion'),
            'required' => false,
            'attr' => [
                'autocomplete' => 'off',
            ]
        ]);

        $builder->add('paymentType', EntityType::class, [
            'label' => $this->translator->trans('Le paiement est réalisé par'),
            'choice_label' => 'label',
            'class' => PaymentType::class,
            'multiple' => false,
            'expanded' => false,
            'attr' => [
                'autocomplete' => 'off',
            ],
            'choice_attr' => function(PaymentType $paymentType)
            {
                return [
                    'data-is-bank-needed' => $paymentType->isBankneeded(),
                ];
            },
            'required' => true,
        ]);

        $builder->add('membershipAmount', MoneyType::class, [
            'label' => $this->translator->trans('Soit une adhésion d\'un montant de'),
            'attr' => [
                'readonly' => 'readonly',
            ],
            'required' => true,
        ]);

        $builder->add('donationAmount', MoneyType::class, [
            'label' => $this->translator->trans('Et un don d\'un montant de'),
            'attr' => [
                'readonly' => 'readonly'
            ],
            'required' => false,
        ]);

        $builder->add('paymentAmount', MoneyType::class, [
            'label' => $this->translator->trans('Le montant du paiement est de'),
            'required' => true,
            'attr' => [
                'min' => '1',
            ]
        ]);

        $builder->add('paymentDate_received', DateType::class, [
            'label' => $this->translator->trans('Le'),
            'widget' => 'single_text',
            'required' => true,
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
            'choices' => $options['selectablePeople'],
            'choice_label' => function (People $people) {
                $peopleDenomination = ($people->getDenomination() != null) ? $people->getDenomination()->getLabel() . ' ' : '';
                return $peopleDenomination . $people->getFirstName() . ' ' . strtoupper($people->getLastName());
            },
            'placeholder' => 'Choisissez une personne',
            'multiple' => false,
            'expanded' => false,
            'required' => true,
        ]);

        $builder->add('members', EntityType::class, [
            'class' => People::class,
            'choices' => $options['selectablePeople'],
            'choice_label' => function (People $people) {
                return $people->getFirstName() . ' ' . $people->getLastName();
            },
            'multiple' => true,
            'expanded' => true,
        ]);

        $builder->add('newMember', EntityType::class, [
            'label' => $this->translator->trans('Choisir les personnes concernées'),
            'class' => People::class,
            'choices' => $options['selectablePeople'],
            'choice_label' => function (People $people) {
                $peopleDenomination = ($people->getDenomination() != null) ? $people->getDenomination()->getLabel() . ' ' : '';
                return $peopleDenomination . $people->getFirstName() . ' ' . strtoupper($people->getLastName());
            },
            'multiple' => false,
            'expanded' => false,
            'attr' => [
                'data-toggle' => 'select2',
                'data-placeholder' => 'Sélectionnez une personne pour l\'ajouter',
                'data-disabled-placeholder' => 'Nombre maximum d\'adhérent·e atteint',
            ],
            'required' => false,
            'help' => $this->translator->trans('adhérent·e·s maximum pour une adhésion de type')
        ]);

        $builder->add('bank', EntityType::class, [
            'class' => Bank::class,
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'label' => $this->translator->trans('Banque'),
            'attr' => [
                'data-toggle' => 'select2',
            ],
            'placeholder' => $this->translator->trans('Sélectionnez une banque'),
        ]);

        $builder->add('check_number', TextType::class, [
            'label' => $this->translator->trans('Numéro du chèque'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateMembershipFDO::class,
            'selectablePeople' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_membership';
    }
}
