<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterUserTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'attr' => ['placeholder' => 'Enter your first name'],
                'label' => 'First name'
            ])
            ->add('last_name', TextType::class, [
                'attr' => ['placeholder' => 'Enter your last name'],
                'label' => 'Last name'
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Enter your email'],
                'label' => 'Email'
            ])
            // use RepeatedType to create two password fields: one for entry and one for confirmation
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => ['placeholder' => 'Enter your password', 'class' => 'mb-1'],
                    'hash_property_path' => 'password' // the password will be automatically hashed and stored in the 'password' property of the object
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => ['placeholder' => 'Repeat your password'],
                ],
                // prevent automatic mapping. This means Symfony will not attempt to map the "plainPassword" form field 
                // to a non-existent "plainPassword" property in the User entity.
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
                'label' => 'Register'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
