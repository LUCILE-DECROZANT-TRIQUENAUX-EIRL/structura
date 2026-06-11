<?php

namespace App\Form;

use App\Entity\DonationOrigin;
use App\FormDataObject\GenerateTagFDO;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenerateTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('membership_years', ChoiceType::class, [
                'label' => 'Année',
                'choices' => $options['availableMembershipYears'],
                'multiple' => true,
                'expanded' => true,
                'placeholder' => 'Sélectionner une ou plusieurs année(s)',
                'required' => false,
            ])
            ->add('departments', ChoiceType::class, [
                'label' => 'Département',
                'choices' => [
                    GenerateTagFDO::DEPARTMENT_AIN => GenerateTagFDO::DEPARTMENT_AIN,
                    GenerateTagFDO::DEPARTMENT_ISERE => GenerateTagFDO::DEPARTMENT_ISERE,
                    GenerateTagFDO::DEPARTMENT_LOIRE => GenerateTagFDO::DEPARTMENT_LOIRE,
                    GenerateTagFDO::DEPARTMENT_RHONE => GenerateTagFDO::DEPARTMENT_RHONE,
                    GenerateTagFDO::DEPARTMENT_OTHER => GenerateTagFDO::DEPARTMENT_OTHER,
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('physical_mail_only', CheckboxType::class, [
                'label' => ' ',
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->add('donation_years', ChoiceType::class, [
                'label' => 'Année',
                'choices' => $options['availableDonationYears'],
                'multiple' => true,
                'expanded' => true,
                'placeholder' => 'Sélectionner une ou plusieurs année(s)',
                'required' => false,
            ])
            ->add('donation_origins', EntityType::class, [
                'label' => 'Origine',
                // looks for choices from this entity
                'class' => DonationOrigin::class,
                // uses the label property as the visible option string
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GenerateTagFDO::class,
            'availableMembershipYears' => null,
            'availableDonationYears' => null,
        ]);
    }
}

