<?php

namespace App\Form\ComparisonModule;

use App\Constants;
use App\Entity\ComparisonModule\Configuration\StandaloneConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class StandaloneConfigurationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, array(
                'label' => 'Base URL for Documents',
                'required' => false,
                'attr' => array('placeholder' => 'either a URI (http://...), or leave empty for using local files'),
            ))
            ->add('presentationMode', ChoiceType::class, array(
                'choices' => array(
                    'iframe' => Constants::IFRAME_MODE,
                    'Document information' => Constants::DOC_INFO_MODE,
                    'Image' => Constants::IMAGE_MODE),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Presentation Mode',
            ))
            ->add('presentationFields', ChoiceType::class, array(
                'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'How many fields do you want to use?',
            ))
            ->add('presentationFieldName_1', TextType::class, array(
                'required' => false,
                'empty_data' => '',
                'attr' => array('placeholder' => 'e.g. Title'),
                'label' => 'Field 1',
            ))
            ->add('presentationFieldName_2', TextType::class, array(
                'required' => false,
                'empty_data' => '',
                'attr' => array('placeholder' => 'e.g. Author'),
                'label' => 'Field 2',
            ))
            ->add('presentationFieldName_3', TextType::class, array(
                'required' => false,
                'empty_data' => '',
                'attr' => array('placeholder' => 'e.g. Abstract'),
                'label' => 'Field 3',
            ))
            ->add('presentationFieldName_4', TextType::class, array(
                'required' => false,
                'empty_data' => '',
                'attr' => array('placeholder' => 'e.g. Keywords'),
                'label' => 'Field 4',
            ))
            ->add('evalButton_left', TextType::class, array(
                'label' => 'Labeling of evaluation button (left)'
            ))
            ->add('evalButton_right', TextType::class, array(
                'label' => 'Labeling of evaluation button (right)'
            ))
            ->add('middleButton', TextType::class, array(
                'label' => 'Labeling of tie button'
            ))
            ->add('allowTie', CheckboxType::class, array(
                'label' => 'Allow for ties?',
                'required' => false
            ))
            ->add('groupBy', CheckboxType::class, array('label' => 'Group document display?', 'required' => false));

            $builder->add('groupByCategory', ChoiceType::class, array(
                'choices' => array(
                    'by Document' => Constants::GROUP_BY_DOCUMENT,
                    'by Document Group' => Constants::GROUP_BY_DOC_GROUP),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Grouping category',
            ));

            $builder
                ->add('randomization', CheckboxType::class, array(
                    'label' => 'Randomize document display?',
                    'required' => false
                ))
                ->add('documentOrder', ChoiceType::class, array(
                    'choices' => array(
                        'Left side' => Constants::DOCUMENT_ORDER_LEFT,
                        'Right side' => Constants::DOCUMENT_ORDER_RIGHT),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                    'label' => 'Placement of grouped document',
                    ))
                ->add('submit', SubmitType::class, array(
                'label' => 'Submit changes', 'attr' => ['class' => 'btn btn-secondary']
                ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => StandaloneConfiguration::class,
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
