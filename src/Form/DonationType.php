<?php

namespace App\Form;

use App\Entity\Donation;
use App\Entity\People;
use App\Entity\Payment;
use App\Entity\PaymentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('amount', MoneyType::class, [
                    'attr' => ['placeholder' => '15,50'],
                ])
                ->add('payment_type', EntityType::class, [
                    // looks for choices from this entity
                    'class' => PaymentType::class,
                    // uses the label property as the visible option string
                    'choice_label' => 'label',
                    'multiple' => false,
                    'expanded' => false,
                ])
                ->add('donator', EntityType::class, [
                    // looks for choices from this entity
                    'class' => People::class,
                    // uses firstname and lastname as the visible option string
                    'choice_label' => function($people) {
                        return $people->getFirstName() . ' ' . $people->getLastName();
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
                ->add('comment', TextareaType::class, [
                    'required' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\FormDataObject\UpdateDonationFDO',
        ]);
    }
}
