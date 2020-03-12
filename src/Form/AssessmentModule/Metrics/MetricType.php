<?php

namespace App\Form\AssessmentModule\Metrics;

use App\Constants;
use App\Entity\AssessmentModule\Metrics\Metric;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class MetricType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', ChoiceType::class, array('label' => 'Name of the metric',
                'choices' => Constants::getMetricsNames(),
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                ))
            ->add('forCompleteList', CheckboxType::class, array(
                'label' => 'Calculate on complete result list?',
                'required' => false,
                'data' => true))
            ->add('k', TextType::class, array('label' => '@K', 'empty_data' => -1, 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Metric::class,
        ));
    }

}
