<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\FormDataObject\GenerateTaxReceiptFromFiscalYearFDO;

class GenerateTaxReceiptFromFiscalYearType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fiscalYear', ChoiceType::class, [
                'choices' => $options['availableFiscalYears'],
                'choice_attr' => $options['availableFiscalYearsData'],
                'multiple' => false,
                'expanded' => false,
                'label' => $this->translator->trans('Année'),
                'placeholder' => $this->translator->trans('Sélectionnez une année fiscale'),
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GenerateTaxReceiptFromFiscalYearFDO::class,
            'availableFiscalYears' => null,
            'availableFiscalYearsData' => null,
        ]);
    }
}
