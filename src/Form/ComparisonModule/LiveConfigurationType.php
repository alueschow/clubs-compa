<?php

namespace App\Form\ComparisonModule;

use App\Constants;
use App\Entity\ComparisonModule\Configuration\LiveConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LiveConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('allowTie', CheckboxType::class, array(
                'label' => 'Allow for ties?',
                'required' => false
            ))
            ->add('useBaseWebsite', CheckboxType::class, array(
                'label' => 'Use a base website?',
                'required' => false,
            ))
            ->add('documentOrder', ChoiceType::class, array(
                'choices' => array(
                    'Left side' => Constants::DOCUMENT_ORDER_LEFT,
                    'Right side' => Constants::DOCUMENT_ORDER_RIGHT),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Placement of base website',
            ))
            ->add('evalButton_left', TextType::class, array(
                'label' => 'Labeling of evaluation button (left)'
            ))
            ->add('evalButton_right', TextType::class, array(
                'label' => 'Labeling of evaluation button (right)'))
            ->add('middleButton', TextType::class, array(
                'label' => 'Labeling of tie button'))
            ->add('randomization', CheckboxType::class, array(
                'label' => 'Randomize document display?',
                'required' => false
            ))
            ->add('randomization_participation', CheckboxType::class, array(
                'label' => 'Will users always be asked for comparison?',
                'required' => false))
            ->add('cookie_name', TextType::class, array(
                'label' => 'Name of the Cookie'
            ))
            ->add('cookie_expires', TextType::class, array(
                'label' => 'Cookie expire time (in days)',
            ))
            ->add('participations_per_time_span', TextType::class, array(
                'label' => 'Max. participations for a single user per time span',
            ))
            ->add('time_span', TextType::class, array(
                'label' => 'Time span (in hours)',
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Submit changes',
                'attr' => ['class' => 'btn btn-secondary']
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LiveConfiguration::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_configuration';
    }

}
