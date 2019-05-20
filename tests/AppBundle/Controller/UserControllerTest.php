<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /*public function testLogin()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET ");

        // Get the form
        $form = $crawler->selectButton('Connexion')->form();
        // Fill the login form input
        $form['_username']->setValue('admin');
        $form['_password']->setValue('a');
        // Send the form
        $client->submit($form);

        $this->assertContains(
                'Bienvenue admin !',
                $client->getResponse()->getContent()
        );
    }*/

    /**
     * Connecte au site afin de pouvoir naviguer
     */
    public function connection()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        // Obtient un⋅e utilisateurice qui a le nom admin
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneByUsername('admin');
        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        // Retourne le client avec authentification
        return $client;
    }

    public function accessEditPage()
    {
        $client = $this->connection();
        $crawler = $client->request('GET', '/user/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Vérifie si la page affiche le bon texte
        $this->assertContains(
                'Liste des utilisateurices',
                $client->getResponse()->getContent()
        );

        // Sélectionne le bouton d'édition de l'utilisateur créé précédemment
        $link = $crawler
            ->filter('tr > td > a:contains("")')
            ->last()
            ->link()
        ;

        $crawler = $client->click($link);

        $this->assertContains(
                'Profil de Jean',
                $client->getResponse()->getContent()
        );

        return array($client,$crawler);
    }

    /*public function testCreate()
    {
        $client = $this->connection();
        $crawler = $client->request('GET', '/user/new');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Vérifie si la page affiche le bon texte
        $this->assertContains(
                'Enregistrer',
                $client->getResponse()->getContent()
        );

        // Sélectionne le formulaire et remplis ses valeurs
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

    public function testEdit()
    {
        $editPage = $this->accessEditPage();
        $client = $editPage[0];
        $crawler = $editPage[1];

        $form = $crawler->selectButton('Changer les informations')->form();
        $values = $form->getPhpValues();
        dump($values);
        $resp = $crawler->filter('.form-group')->eq(1);
        $blogTitle  = $resp->text();
        dump($blogTitle);
        $crawler = $client->click($resp->link());
        //dump($values['appbundle_user']['_token']);

        /*$values['appbundle_user']['username'] = 'Jean';
        $values['appbundle_user']['plainPassword']['first'] = 'motdepasse';
        $values['appbundle_user']['plainPassword']['second'] = 'motdepasse';*/

    }
}
