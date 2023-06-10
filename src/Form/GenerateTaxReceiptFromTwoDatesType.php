<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class GenerateTaxReceiptFromTwoDatesType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', DateType::class, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('Du...'),
            ])
            ->add('to', DateType::class, [
                'widget' => 'single_text',
                'label' => $this->translator->trans('Au...'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\FormDataObject\GenerateTaxReceiptFromTwoDatesFDO',
        ]);
    }
}
