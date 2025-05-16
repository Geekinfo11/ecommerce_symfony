<?php

namespace App\Tests;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function Zenstruck\Foundry\faker;
use Zenstruck\Foundry\Test\Factories;

class RegisterUserTest extends WebTestCase
{
    use Factories;

    // this test class checks the registration process of a new user.
    // to run this test, we need a separate test database (so we don't affect real data).
    // create test database using: symfony console doctrine:database:create --env=test
    // then apply the existing migrations to that test database with: symfony console doctrine:migrations:migrate -n --env=test
    public function testRegisterWithValidData(): void
    {
        // creates a "client" that is acting as the browser
        $client = static::createClient();
        $client->request('GET', '/register');

        // clear users table. of course the table in the test database
        UserFactory::truncate();

        // request the registration form page
        // submit the registration form using the button's name/id. in our case here register_user_type_form_submit is the ID.
        $client->submitForm('register_user_type_form_submit', [
            // input name => some random value
            'register_user_type_form[first_name]' => faker()->firstName(),
            'register_user_type_form[last_name]' => faker()->lastName(),
            'register_user_type_form[email]' => 'test@gmail.com',
            'register_user_type_form[plainPassword][first]' => '12345678',
            'register_user_type_form[plainPassword][second]' => '12345678'
        ]);

        // assert that the form submission redirects to the login page
        $this->assertResponseRedirects('/login');

        // when a request returns a redirect response, the client does not follow it automatically. force a redirection afterwards with the followRedirect() method.
        $client->followRedirect();

        // assert that the response content contains the success message after redirection.
        // Note: the flash message "Your account is created successfully" is rendered via JavaScript (using toastr),
        // but since javascript isn't executed in functional tests, we're checking if the raw HTML contains the message text.
        // this works because symfony renders the flash messages into the page before JS renders them.
        $this->assertStringContainsString('Your account is created successfully', $client->getResponse()->getContent());
    }

    public function testRegisterWithInvalidData(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $client->submitForm('register_user_type_form_submit', [
            // input name => some random value
            'register_user_type_form[first_name]' => 'S',
            'register_user_type_form[last_name]' => 'S',
            'register_user_type_form[email]' => 'samexample.com',
            'register_user_type_form[plainPassword][first]' => 'sam1',
            'register_user_type_form[plainPassword][second]' => 'sam1'
        ]);

        $this->assertSelectorExists('.form-error');
    }
}
