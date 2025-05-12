<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        // create a new empty User object to be filled with form data upon form submission
        $user = new User();

        // create a form instance using the RegisterUserTypeForm class, which defines the form's fields and validation rules, and bind it to the User object
        $form = $this->createForm(RegisterUserTypeForm::class, $user);

        // handles the request: if it's a POST (form submitted), it populates the form with submitted data from request then $user object will be automatically populated with the form data
        $form->handleRequest($request);

        // if the form is submitted and valid, persist the user to the database
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form
        ]);
    }
}
