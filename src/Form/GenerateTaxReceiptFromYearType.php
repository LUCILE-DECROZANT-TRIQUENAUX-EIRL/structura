<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\FormDataObject\GenerateTaxReceiptFromYearFDO;

class GenerateTaxReceiptFromYearType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', ChoiceType::class, [
                'choices' => $options['availableYears'],
                'choice_attr' => $options['availableYearsData'],
                'multiple' => false,
                'expanded' => false,
                'label' => $this->translator->trans('Année'),
                'placeholder' => $this->translator->trans('Sélectionnez une année'),
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GenerateTaxReceiptFromYearFDO::class,
            'availableYears' => null,
            'availableYearsData' => null,
        ]);
    }
}
