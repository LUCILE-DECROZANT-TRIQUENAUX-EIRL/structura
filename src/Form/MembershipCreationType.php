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

class MembershipCreationType extends AbstractType
{

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
            'label' => 'Enregister une adhésion de type',
            'help' => '1 adhérent.e.s maximum pour ce type d\'adhésion'
        ]);

        $builder->add('membershipDate_start', DateType::class, [
            'label' => 'L\'adhésion est valable du',
            'widget' => 'single_text',
        ]);

        $builder->add('membershipDate_end', DateType::class, [
            'label' => 'Au',
            'widget' => 'single_text',
        ]);

        $builder->add('membershipFiscal_year', IntegerType::class, [
            'label' => 'Pour l\'année fiscale',
        ]);

        $builder->add('membershipComment', TextareaType::class, [
            'label' => 'Commentaire sur l\'adhésion',
            'required' => false,
        ]);

        $builder->add('paymentType', EntityType::class, [
            'label' => 'Via',
            'choice_label' => 'label',
            'class' => PaymentType::class,
            'multiple' => false,
            'expanded' => false,
        ]);

        $builder->add('membershipAmount', MoneyType::class, [
            'label' => 'Soit une adhésion d\'un montant de',
            'attr' => [
                'readonly' => 'readonly'
            ],
        ]);

        $builder->add('donationAmount', MoneyType::class, [
            'label' => 'Et un don d\'un montant de',
            'attr' => [
                'readonly' => 'readonly'
            ],
            'required' => false,
        ]);

        $builder->add('paymentAmount', MoneyType::class, [
            'label' => 'Le règlement est de',
        ]);

        $builder->add('paymentDate_received', DateType::class, [
            'label' => 'Le',
            'widget' => 'single_text',
            'required' => false,
        ]);

        $builder->add('paymentDate_cashed', DateType::class, [
            'label' => 'Encaissé le',
            'widget' => 'single_text',
            'required' => false,
        ]);

        $builder->add('payer', EntityType::class, [
            'label' => 'Effectué par',
            'class' => People::class,
            'attr' => [
                'readonly' => true,
            ],
            'choices' => [$options['peopleWithNoActiveMembership']],
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
            'class' => People::class,
            'choices' => $options['peopleWithNoActiveMembership'],
            'choice_label' => function (People $people) {
                $peopleDenomination = ($people->getDenomination() != null) ? $people->getDenomination()->getLabel() . ' ' : '';
                return $peopleDenomination . $people->getFirstName() . ' ' . strtoupper($people->getLastName());
            },
            'multiple' => false,
            'expanded' => false,
            'placeholder' => '—',
            'required' => false,
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