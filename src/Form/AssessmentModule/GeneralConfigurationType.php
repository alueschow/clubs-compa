<?php

namespace App\Form\AssessmentModule;

use App\Constants;
use App\Entity\AssessmentModule\Configuration\GeneralConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class GeneralConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, array(
                'label' => 'Base URL for Documents',
                'required' => false
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
            ->add('queryStyle', ChoiceType::class, array(
                'choices' => array(
                    'only Query' => 'only_query',
                    'only Topic' => 'only_topic',
                    'Both' => 'both',
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Which information should be shown?',
            ))
            ->add('queryHeadingName', TextType::class, array(
                'required' => false,
                'empty_data' => '',
                'attr' => array('placeholder' => 'e.g. Query'),
                'label' => 'Query heading',
            ))
            ->add('topicHeadingName', TextType::class, array(
                'required' => false,
                'empty_data' => '',
                'attr' => array('placeholder' => 'e.g. Topic'),
                'label' => 'Topic heading',
            ))
            ->add('documentHeading', CheckboxType::class, array(
                'required' => false,
                'label' => 'Show document heading?',
            ))
            ->add('documentHeadingName', TextType::class, array(
                'required' => false,
                'empty_data' => '',
                'attr' => array('placeholder' => 'e.g. Retrieved Document'),
                'label' => 'Document heading',
            ))
            ->add('groupBy', CheckboxType::class, array('label' => 'Group assessments?', 'required' => false))
            ->add('groupByCategory', ChoiceType::class, array(
                'choices' => array(
                    'by Query' => Constants::GROUP_BY_QUERY,
                    'by Document' => Constants::GROUP_BY_DOCUMENT,
                    'by Document Group' => Constants::GROUP_BY_DOC_GROUP),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Grouping category',
            ))
            ->add('skippingAllowed', CheckboxType::class, array('label' => 'Allow for skipping documents?', 'required' => false))
            ->add('skippingOptions', ChoiceType::class, array(
                'choices' => array(
                    'Reject rating' => Constants::SKIP_REJECT_OPTION,
                    'Postpone rating' => Constants::SKIP_POSTPONE_OPTION,
                    'Both' => Constants::SKIP_BOTH_OPTION),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Skipping options',
            ))
            ->add('skippingComment', CheckboxType::class, array('label' => 'Ask user for comment on rejection?', 'required' => false))
            ->add('loadingNewDocument', CheckboxType::class, array('label' => 'Allow for loading new documents by the user?', 'required' => false))
            ->add('userProgressBar', CheckboxType::class, array('label' => 'Show user\'s progress', 'required' => false))
            ->add('totalProgressBar', CheckboxType::class, array('label' => 'Show total progress', 'required' => false))
            ->add('submit', SubmitType::class, array('label' => 'Submit changes', 'attr' => ['class' => 'btn btn-secondary']))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GeneralConfiguration::class,
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
