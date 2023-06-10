<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\People;
use App\FormDataObject\MemberSelectionFDO;
use App\Repository\PeopleRepository;

class MemberSelectionType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('newMembers', EntityType::class, [
            'class' => People::class,
            'choices' => $options['peoples'],
            'choice_label' => function (People $people) {
                return ' ';
            },
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MemberSelectionFDO::class,
            'peoples' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_member_selection';
    }

}
