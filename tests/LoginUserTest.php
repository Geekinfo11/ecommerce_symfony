<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginUserTest extends WebTestCase
{
    public function testLoginWithValidData(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Login', [
            '_username' => 'test@gmail.com',
            '_password' => '12345678',
            '_target_path' => '/account'
        ]);

        $this->assertResponseRedirects('/account');
    }

    public function testLoginWithInvalidUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertTrue(true);

        $client->submitForm('Login', [
            '_username' => 'example@gmail.com',
            '_password' => 'example123456',
            '_target_path' => '/account'
        ]);

        $client->followRedirect();

        $this->assertSelectorExists('.form-error');
    }

    public function testLoginWithInvalidData(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertTrue(true);

        $client->submitForm('Login', [
            '_username' => 'example',
            '_password' => '123',
            '_target_path' => '/account'
        ]);

        $client->followRedirect();

        $this->assertSelectorExists('.form-error');
    }
}
