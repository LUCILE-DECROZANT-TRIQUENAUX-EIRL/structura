<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /**
     * Connect to the website while being logged in
     * Logs in with (admin, password : a)
     */
    public function connection()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        // Get the user (has to exist in the database)
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneByUsername('admin');
        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        // Return the client
        return $client;
    }

    public function accessEditPage()
    {
        $client = $this->connection();
        $crawler = $client->request('GET', '/user/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains(
                'Liste des utilisateurices',
                $client->getResponse()->getContent()
        );
        // Select the button of the user created for the test
        // Wont work if there are already more than 10 users in the database
        $link = $crawler
            ->filter('tr > td > a:contains("")')
            ->last()
            ->link()
        ;
        $crawler = $client->click($link);
        return array($client,$crawler);
    }

    /**
     * Create a new user
     */
     /*
    public function testCreate()
    {
        $client = $this->connection();
        $crawler = $client->request('GET', '/user/new');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Vérifie si la page affiche le bon texte
        $this->assertContains(
                'Enregistrer',
                $client->getResponse()->getContent()
        );

        // Select the form and fill its values
        $form = $crawler->selectButton(' Créer')->form();
        $values = $form->getPhpValues();
        $values['appbundle_user']['username'] = 'Jean';
        $values['appbundle_user']['plainPassword']['first'] = 'motdepasse';
        $values['appbundle_user']['plainPassword']['second'] = 'motdepasse';

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $crawler = $client->followRedirect();
        $this->assertContains(
                'Jean',
                $client->getResponse()->getContent()
        );
    }*/

    /**
     * Delete the user created for the test
     */
    public function testDelete()
    {
        $editPage = $this->accessEditPage();
        $client = $editPage[0];
        $crawler = $editPage[1];

        //$button = $crawler->selectButton(' Supprimer l\'utilisateurice');
        // https://stackoverflow.com/questions/29149124/how-to-click-on-a-button-in-phpunit-symfony2#comment46549861_29149760
        // https://stackoverflow.com/questions/40222621/domcrawler-how-to-click-button-unable-to-navigate-from-a-button-tag
        $button = $crawler
            ->filter('button:contains(" Supprimer l\'utilisateurice")')
        ;
        //$crawler = $client->click($button->link());

        //dump($button->html());
    }

    /**
     * Change the responsability of the user created for the test
     * Affects the responsability Gestionnaire 1
     */
    /*public function testEditResponsability()
    {
        $editPage = $this->accessEditPage();
        $client = $editPage[0];
        $crawler = $editPage[1];

        $form = $crawler->selectButton('Changer les informations')->form();
        $form['appbundle_user']['responsabilities'][1]->tick();
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains('Les informations ont bien été modifiées',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Chang the username
     */
    /*public function testEditPseudo()
    {
        $editPage = $this->accessEditPage();
        $client = $editPage[0];
        $crawler = $editPage[1];

        $form = $crawler->selectButton('Changer les informations')->form();
        $values = $form->getPhpValues();
        $values['appbundle_user']['username'] = 'René';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $crawler = $client->followRedirect();
        $this->assertContains('René',
                $client->getResponse()->getContent()
        );
        $this->assertContains('Les informations ont bien été modifiées',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Change the password
     */
    /*public function testEditPassword()
    {
        $editPage = $this->accessEditPage();
        $client = $editPage[0];
        $crawler = $editPage[1];

        $form = $crawler->selectButton('Changer le mot de passe')->form();
        $values = $form->getPhpValues();
        $values['appbundle_password']['oldPassword'] = 'motdepasse';
        $values['appbundle_password']['plainPassword']['first'] = 'password';
        $values['appbundle_password']['plainPassword']['second'] = 'password';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $this->assertContains('Le mot de passe a bien été modifié',
                $client->getResponse()->getContent()
        );
    }*/
}
