<?php

namespace App\Form\AssessmentModule;

use App\Entity\AssessmentModule\Configuration\RatingOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class RatingOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Name'))
            ->add('short_name', TextType::class, array('label' => 'Short name'))
            ->add('priority', TextType::class, array('label' => 'Priority'))
            ->add('used_in_metrics', CheckboxType::class, array('label' => 'Use for metrics calculation?', 'required' => false))
            ->add('submit', SubmitType::class, array('label' => 'Add', 'attr' => ['class' => 'btn btn-secondary']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RatingOption::class,
        ));
    }

}
