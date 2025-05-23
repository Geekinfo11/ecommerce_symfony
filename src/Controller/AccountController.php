<?php

namespace App\Controller;

use App\Form\PasswordUserTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    #[Route('/account/edit_password', 'app_account_edit_password')]
    public function password(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        // get authenticated user
        $user = $this->getUser();

        $form = $this->createForm(PasswordUserTypeForm::class, $user, [
            // pass the passwordHasher to the PasswordUserTypeForm
            'passwordHasher' => $userPasswordHasherInterface
        ]);

        // this processes the request and populates the form fields with submitted data.
        // it also updates the $user object (bound to the form) with the new values.
        $form->handleRequest($request);

        // after form is submitted and valid. symfony detects hash_property_path, hashes the plainPassword in PasswordUserTypeForm, and sets it on User::$password
        // even though plainPassword is not mapped.
        if ($form->isSubmitted() && $form->isValid()) {
            // Doctrine automatically tracks changes to any managed entities. since $user is already known to Doctrine (fetched earlier when the user was authenticated), Doctrine is watching it.
            // flush tells Doctrine: save all tracked changes to database.
            $entityManager->flush();

            // flash a message to the user
            $this->addFlash('success', 'Your password is updated successfully!');
        }

        return $this->render('account/edit_password.html.twig', [
            'passwordForm' => $form
        ]);
    }
}
