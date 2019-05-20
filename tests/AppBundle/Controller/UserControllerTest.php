<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
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

    /**
     * Change la responsabilité de l'utilisateur créé plus tôt
     * Lui affecte la reponsabilité Gestionnaire 1
     */
    public function testEditResponsability()
    {
        $editPage = $this->accessEditPage();
        $client = $editPage[0];
        $crawler = $editPage[1];

        $form = $crawler->selectButton('Changer les informations')->form();
        $form['appbundle_user']['responsabilities'][1]->tick();
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        // J'essaye de vérifier si c'est checked mais je ne trouve pas... miss tère...
        $this->assertContains('Les informations ont bien été modifiées',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Change le pseudonyme
     */
    public function testEditPseudo()
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
     * Change le mot de passe
     */
    public function testEditPassword()
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
    }

    public function testDelete()
    {
        $editPage = $this->accessEditPage();
        $client = $editPage[0];
        $crawler = $editPage[1];

        // $button = $crawler
        //     ->filter('button:contains(" Supprimer l\'utilisateurice")')
        // ;

        $form = $crawler->selectButton('delete_button')->form();
        $crawler = $client->submit($form);

        //$button = $crawler->selectButton(' Supprimer l\'utilisateurice');
        // https://stackoverflow.com/questions/29149124/how-to-click-on-a-button-in-phpunit-symfony2#comment46549861_29149760
        // https://stackoverflow.com/questions/40222621/domcrawler-how-to-click-button-unable-to-navigate-from-a-button-tag
        // $button = $crawler
        //     ->filter('button:contains("&nbsp;Supprimer l&#039;utilisateurice")')
        // ;
        // $crawler = $client->submit($form->link());

        //dump($button->html());

        $this->assertContains('Redirecting to /user/',
                $client->getResponse()->getContent()
        );
    }
}
