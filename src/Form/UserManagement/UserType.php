<?php

namespace App\Form\UserManagement;

use App\Constants;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class UserType extends AbstractType
{
    private $document_groups;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->document_groups = $options['document_groups'];

        $builder
            ->add('username', TextType::class, array('label' => 'Username'))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
//            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Administrator' => Constants::ROLE_ADMIN,
                    'Assessment' => Constants::ROLE_ASSESSMENT,
                    'Comparison' => Constants::ROLE_COMPARISON),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => 'Role',
            ))
            ->add('groups', ChoiceType::class, array(
                'choices' => $this->document_groups,
                'expanded' => true,
                'multiple' => true,
                'label' => 'Group',
                'label_attr' => array(
                    'class' => 'checkbox-inline'
                )
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Submit user', 'attr' => ['class' => 'btn btn-secondary'])
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'document_groups' => null,
        ));
    }

}
