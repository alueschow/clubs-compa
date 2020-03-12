<?php

namespace App\Form\AssessmentModule\Metrics;

use App\Entity\AssessmentModule\Metrics\MetricConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MetricConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('max_length', TextType::class, array('label' => 'Max. length of result set'))
            ->add('limited', CheckboxType::class, array('label' => 'Limit result list for metric calculation?', 'required' => false))
            ->add('round_precision', TextType::class, array('label' => 'Round metric values to ... places'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MetricConfiguration::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'metric_config';
    }

}
