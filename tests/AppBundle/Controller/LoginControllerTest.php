<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends WebTestCase
{
    /*
     * Test the login form
     * Logins with (admin, password : a)
     */
    public function testLogin()
    {
        // Create a new client to browse the app
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET ");
        // Get the form
        $form = $crawler->selectButton('Connexion')->form();
        // Fill the login form input
        $form['_username']->setValue('admin');
        $form['_password']->setValue('a');
        // Send the form
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains(
                'Bienvenue admin.' ,
                $client->getResponse()->getContent()
        );
    }
}
?>
