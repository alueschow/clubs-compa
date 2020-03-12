<?php

namespace App\Form\ComparisonModule;

use App\Entity\ComparisonModule\Configuration\Website;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class WebsiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('website_name', TextType::class, array('label' => 'Name of the Website'))
            ->add('website_url', TextType::class, array('label' => 'URL (incl. GET parameters)'))
            ->add('submit', SubmitType::class, array('label' => 'Submit website', 'attr' => ['class' => 'btn btn-secondary']))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Website::class
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
