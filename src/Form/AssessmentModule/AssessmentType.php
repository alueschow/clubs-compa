<?php

namespace App\Form\AssessmentModule;

use App\Entity\AssessmentModule\Assessment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AssessmentType extends AbstractType
{

    private $rating_levels;
    private $rating_heading;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->rating_levels = $options['rating_levels'];
        $this->rating_heading = $options['rating_heading'];

        $builder
            ->add('rating', ChoiceType::class, array(
                'choices' => $this->rating_levels,
                'expanded' => true,
                'multiple' => false,
                'label' => $this->rating_heading,
            ))
            ->add('submit', SubmitType::class,
                array(
                    'label' => 'Submit',
                    'attr' => array('class' => 'btn btn-dark', 'style' => 'margin-top: 10%')
                ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Assessment::class,
            'rating_levels' => null,
            'rating_heading' => null
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
