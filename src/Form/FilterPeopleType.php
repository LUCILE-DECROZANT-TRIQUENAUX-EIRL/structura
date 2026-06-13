<?php

namespace App\Form;

use App\Entity\DonationOrigin;
use App\FormDataObject\FilterPeopleFDO;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterPeopleType extends AbstractType
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
                    FilterPeopleFDO::DEPARTMENT_AIN => FilterPeopleFDO::DEPARTMENT_AIN,
                    FilterPeopleFDO::DEPARTMENT_ISERE => FilterPeopleFDO::DEPARTMENT_ISERE,
                    FilterPeopleFDO::DEPARTMENT_LOIRE => FilterPeopleFDO::DEPARTMENT_LOIRE,
                    FilterPeopleFDO::DEPARTMENT_RHONE => FilterPeopleFDO::DEPARTMENT_RHONE,
                    FilterPeopleFDO::DEPARTMENT_OTHER => FilterPeopleFDO::DEPARTMENT_OTHER,
                ],
                'multiple' => true,
                'expanded' => true,
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

        if ($options['isForTag']) {
            $builder->add('physical_mail_only', CheckboxType::class, [
                'label' => ' ',
                'label_attr' => ['class' => 'switch-custom'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FilterPeopleFDO::class,
            'availableMembershipYears' => null,
            'availableDonationYears' => null,
            'isForTag' => false,
        ]);
    }
}

