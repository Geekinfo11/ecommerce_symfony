<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Current password',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'placeholder' => 'Enter current password',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'max' => 50
                    ])
                ],
                'mapped' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
                    'label_attr' => [
                        'class' => 'form-label'
                    ],
                    'attr' => [
                        'placeholder' => 'Enter your new password',
                        'class' => 'form-control'
                    ],
                    'hash_property_path' => 'password', // the password will be automatically hashed and stored in the 'password' property of the User object
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'label_attr' => [
                        'class' => 'form-label'
                    ],
                    'attr' => [
                        'placeholder' => 'Repeat your new password',
                        'class' => 'form-control'
                    ],
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
                'label' => 'Change Password'
            ])
            // add an event listener to form submmition to check if the current password provided is the same
            // as the one saved in the database.
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                // at this point, the form has been submitted, but Symfony has not yet handled the request.
                // that means we only have access to the original data passed when the form was created.
                // in AccountController, for example:
                // $form = $this->createForm(PasswordUserTypeForm::class, $user);
                // so here, $form->getData() will return the $user object passed during form creation,
                // not the data from the HTTP request — because $form->handleRequest($request) hasn’t been called yet which populate the form with $request data.
                // so in thi scenario $event->getForm()->getConfig()->getOptions()['data'] similar to: $event->getData()
                $form = $event->getForm();

                // get the original User object passed to the form. so this always returns the data passed to the from when created
                // unlike $form->getData() which changes if $form->handleRequest($request) is executed.
                $user = $form->getConfig()->getOptions()['data'];

                // get the currentPassword field entered by the user in the form
                $currentPassword = $form->get('currentPassword')->getData();

                // check password validity
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];
                $isPwdValid = $passwordHasher->isPasswordValid($user, $currentPassword);

                // add an error
                if (!$isPwdValid) {
                    $form->get('currentPassword')->addError(new FormError('the current password field is invalid.'));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // link this form to the User entity. When this form is submitted, Symfony will map the form fields
            // to an instance of the User class.
            // if a form field does not exist in the User entity, set 'mapped' => false on that field to prevent mapping.
            'data_class' => User::class,
            // declare the options passed to this form from AccountController.
            'passwordHasher' => null
        ]);
    }
}
