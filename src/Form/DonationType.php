<?php

namespace App\Form;

use App\Entity\Bank;
use App\Entity\Donation;
use App\Entity\DonationOrigin;
use App\Entity\People;
use App\Entity\Payment;
use App\Entity\PaymentType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Contracts\Translation\TranslatorInterface;

class DonationType extends AbstractType
{
    public $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('amount', MoneyType::class, [
                    'attr' => ['placeholder' => '15,50'],
                    'required' => true,
                    'invalid_message' => 'Vous devez entrer une valeur comprise entre 1 € et 9 000 000 €.',
                    'constraints' => [
                        new Range([
                            'min' => 1,
                            'max' => 9000000,
                            'maxMessage' => 'Vous devez entrer une valeur comprise entre 1 € et 9 000 000 €.',
                        ])
                    ],
                ])
                ->add('payment_type', EntityType::class, [
                    'label' => $this->translator->trans('Via'),
                    // uses the label property as the visible option string
                    'choice_label' => 'label',
                    // looks for choices from this entity
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
                ])
                ->add('donator', EntityType::class, [
                    // looks for choices from this entity
                    'class' => People::class,
                    'attr' => [
                        'data-toggle' => 'select2'
                    ],
                    // uses firstname and lastname as the visible option string
                    'choice_label' => function($people) {
                        return mb_strtoupper($people->getLastName(), 'UTF-8') . ' ' . $people->getFirstName();
                    },
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.lastName', 'ASC');
                    },
                    'multiple' => false,
                    'expanded' => false,
                ])
                ->add('donation_date', DateType::class, [
                    'widget' => 'single_text',
                ])
                ->add('cashed_date', DateType::class, [
                    'widget' => 'single_text',
                    'required' => false,
                ])
                ->add('donation_origin', EntityType::class, [
                    // looks for choices from this entity
                    'class' => DonationOrigin::class,
                    'attr' => [
                        'data-toggle' => 'select2'
                    ],
                    // uses the label property as the visible option string
                    'choice_label' => 'label',
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                ])
                ->add('comment', TextareaType::class, [
                    'required' => false,
                ])
        ;
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
            'attr' => [
                'maxlength' => 40,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\FormDataObject\UpdateDonationFDO',
        ]);
    }
}
