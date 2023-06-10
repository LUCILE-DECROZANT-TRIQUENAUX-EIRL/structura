<?php

namespace App\Form;

use App\FormDataObject\AssociationLogoFDO;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociationLogoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logo', FileType::class, [
                    'label' => 'Logo de l\'association',
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
            'data_class' => AssociationLogoFDO::class,
        ]);
    }
}
