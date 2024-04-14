<?php

namespace App\Form;

use App\FormDataObject\GenerateTagFDO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenerateTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('year', ChoiceType::class, [
                'label' => 'Année d\'adhésion',
                'choices' => $options['availableYears'],
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Sélectionner une année',
                'required' => false,
            ])
            ->add('departments', ChoiceType::class, [
                'label' => 'Départements',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GenerateTagFDO::class,
            'availableYears' => null,
        ]);
    }
}

