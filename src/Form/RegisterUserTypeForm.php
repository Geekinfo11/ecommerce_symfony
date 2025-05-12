<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'attr' => ['placeholder' => 'Enter your first name'],
                'label' => 'First name',
                // add validations
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 50
                    ])
                ]
            ])
            ->add('last_name', TextType::class, [
                'attr' => ['placeholder' => 'Enter your last name'],
                'label' => 'Last name',
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 50
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Enter your email'],
                'label' => 'Email',
                'constraints' => [
                    new Email([
                        'message' => 'The email "{{value}}" is not a valid email.'
                    ])
                ]
            ])
            // use RepeatedType to create two password fields: one for entry and one for confirmation
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => ['placeholder' => 'Enter your password'],
                    'hash_property_path' => 'password', // the password will be automatically hashed and stored in the 'password' property of the object

                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => ['placeholder' => 'Repeat your password'],
                ],
                // prevent automatic mapping. This means Symfony will not attempt to map the "plainPassword" form field 
                // to a non-existent "plainPassword" property in the User entity.
                'mapped' => false,
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 50
                    ])
                ]
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
            // prevent registration of a new user if the email already exists in the database
            'constraints' => [
                new UniqueEntity(fields: ['email']),
            ],
        ]);
    }
}
