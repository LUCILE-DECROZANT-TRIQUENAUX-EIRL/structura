<?php
namespace Tests\App\Controller;

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

        return array($client,$crawler);
    }

    /*
     * Tries to login with a user that shouldn't exist
     * Then tries to login with a user taht exists but wrong password
     */
    public function testLoginFalse()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET ");
        $form = $crawler->selectButton('Connexion')->form();
        // Use of values so I can redirect with the values
        $values = $form->getPhpValues();

        $values['_username'] = 'adminnn';
        $values['_password'] = 'a';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $crawler = $client->followRedirect();
        $this->assertContains(
                'utilisateur ou mot de passe invalide.' ,
                $client->getResponse()->getContent()
        );

        $values['_username'] = 'admin';
        $values['_password'] = 'admin';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $crawler = $client->followRedirect();
        $this->assertContains(
                'utilisateur ou mot de passe invalide.' ,
                $client->getResponse()->getContent()
        );
    }

    public function testLogout()
    {
        $login = $this->testLogin();
        $client = $login[0];
        $crawler = $login[1];

        // Selects the menu to logout
        $link = $crawler
            ->filter('a:contains("admin")')
            ->link()
        ;
        $crawler = $client->click($link);

        // Log outs the user
        $link = $crawler
            ->filter('a:contains("Me déconnecter")')
            ->link()
        ;
        $crawler = $client->click($link);
        // 2 redirects
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $this->assertContains(
                'Bienvenue sur la base de données' ,
                $client->getResponse()->getContent()
        );
    }
}
?>
