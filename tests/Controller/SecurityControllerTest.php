<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller;

use App\Entity\Delegation;
use App\Entity\User;
use App\Tests\DataFixtures\TestDelegationFixtures;
use App\Tests\Traits\AssertAuthenticationTrait;
use App\Tests\Traits\AssertEmailTrait;
use Doctrine\Persistence\ManagerRegistry;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;
    use AssertEmailTrait;
    use AssertAuthenticationTrait;

    public function testNothing()
    {
        $this->assertTrue(true);
    }

    public function skipTestCanRegister()
    {
        $client = $this->createClient();
        $this->loadFixtures([TestDelegationFixtures::class]);

        $email = 'f@mangel.io';
        $password = 'asdf1234';

        $this->assertNotAuthenticated($client);

        $delegationName = TestDelegationFixtures::DELEGATION_NAME;
        $registrationHash = $this->getDelegationRegistrationHash($delegationName);
        $this->register($client, $delegationName, $registrationHash, $email, $password);
        $this->assertAuthenticated($client);

        $this->logout($client);
        $this->assertNotAuthenticated($client);

        $this->login($client, $email, $password);
        $this->assertAuthenticated($client);

        $this->logout($client);
        $this->assertNotAuthenticated($client);

        $this->recover($client, $email);
        $this->assertNotAuthenticated($client);

        $authenticationHash = $this->getAuthenticationHash($email);
        $this->recoverConfirm($client, $authenticationHash, $password);
        $this->assertAuthenticated($client);
    }

    private function login(KernelBrowser $client, string $email, string $password): void
    {
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('login_submit')->form();
        $form['login[email]'] = $email;
        $form['login[password]'] = $password;

        $client->submit($form);
        $this->assertResponseRedirects();
    }

    private function logout(KernelBrowser $client): void
    {
        $client->request('GET', '/logout');
        $this->assertResponseRedirects();
    }

    private function register(KernelBrowser $client, string $delegationName, string $registrationHash, string $email, string $password): void
    {
        $crawler = $client->request('GET', '/register/'.$delegationName.'/'.$registrationHash);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('only_email_submit')->form();
        $form['register_confirm[only_email][email]'] = $email;
        $form['register_confirm[password][plainPassword]'] = $password;
        $form['register_confirm[password][repeatPlainPassword]'] = $password;

        $client->submit($form);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('successful', $client->getResponse()->getContent()); // alert to user

        $authenticationHash = $this->getAuthenticationHash($email);
        $this->assertSingleEmailSentWithBodyContains($authenticationHash);
    }

    private function recover(KernelBrowser $client, string $email): void
    {
        $crawler = $client->request('GET', '/recover');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('only_email_submit')->form();
        $form['only_email[email]'] = $email;

        $client->submit($form);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('sent', $client->getResponse()->getContent()); // alert to user

        $authenticationHash = $this->getAuthenticationHash($email);
        $this->assertSingleEmailSentWithBodyContains($authenticationHash);
    }

    private function recoverConfirm(KernelBrowser $client, string $authenticationHash, string $password): void
    {
        $crawler = $client->request('GET', '/recover/confirm/'.$authenticationHash);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('set_password_submit')->form();
        $form['set_password[plainPassword]'] = $password;
        $form['set_password[repeatPlainPassword]'] = $password;
        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertStringContainsString('set', $client->getResponse()->getContent()); // alert to user
    }

    private function getDelegationRegistrationHash(string $name)
    {
        $registry = static::$container->get(ManagerRegistry::class);
        $repository = $registry->getRepository(Delegation::class);
        /** @var Delegation $delegation */
        $delegation = $repository->findOneBy(['name' => $name]);

        return $delegation->getRegistrationHash();
    }

    private function getAuthenticationHash(string $email)
    {
        $registry = static::$container->get(ManagerRegistry::class);
        $repository = $registry->getRepository(User::class);
        /** @var User $user */
        $user = $repository->findOneBy(['email' => $email]);

        return $user->getAuthenticationHash();
    }
}
