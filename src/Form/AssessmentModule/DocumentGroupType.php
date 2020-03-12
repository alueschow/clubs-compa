<?php

namespace App\Form\AssessmentModule;

use App\Entity\AssessmentModule\Configuration\DocumentGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class DocumentGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Name'))
            ->add('short_name', TextType::class, array('label' => 'Short name'))
            ->add('nr_of_max_evaluations', TextType::class, array('label' => 'Nr of ratings'))
            ->add('submit', SubmitType::class, array('label' => 'Add document group', 'attr' => ['class' => 'btn btn-secondary']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DocumentGroup::class,
        ));
    }

}
