<?php

namespace App\Form;

use App\FormDataObject\AssociationTreasurerSignatureFDO;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociationTreasurerSignatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('treasurerSignature', FileType::class, [
                    'label' => 'Signature de lae trésorie.re',
                    'constraints' => [
                        new Image([
                            'maxSize' => '1024k',
                            'mimeTypesMessage' => 'Le fichier téléversé doit être une image.',
                        ])
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssociationTreasurerSignatureFDO::class,
        ]);
    }
}
