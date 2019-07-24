<?php
namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class UserControllerTest extends WebTestCase
{
    /*****************************/
    /* ~~~~ Utility methods ~~~~ */
    /*****************************/

    /**
     * Connect to the website while being logged in
     * Logs in with (username : admin, password : a)
     */
    public function connection()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');

        // Get the admin user
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('admin');
        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());

        // Set the session
        $session->set('_security_main', serialize($token));
        $session->save();

        // Set the cookie
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        // Return the client
        return $client;
    }

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the user list page.
     */
    public function testAccessUserListPage()
    {
        $client = $this->connection();
        $crawler = $client->request('GET', '/user/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains(
                'Liste des utilisateurices',
                $client->getResponse()->getContent()
        );

        return array($client, $crawler);
    }

    // /**
    //  * Returns a client object and a crawler object.
    //  * The "user" is connected and on the edit page.
    //  */
    // public function accessEditPage()
    // {
    //     $client = $this->connection();

    //     // Get the user (has to exist in the database for now)
    //     $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('admin');

    //     $editPageUrl = '/user/' . $person->getId() . '/edit';

    //     $crawler = $client->request('GET', $editPageUrl);
    //     $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    //     $this->assertContains(
    //             'Édition de',
    //             $client->getResponse()->getContent()
    //     );

    //     // Keeping this snippet to test the buttons inside the table

    //     // Select the button of the user created for the test
    //     // Wont work if there are already more than 10 users in the database
    //     // $link = $crawler
    //     //     ->filter('tr > td > a:contains("")')
    //     //     ->last()
    //     //     ->link()
    //     // ;
    //     // $crawler = $client->click($link);

    //     return array($client, $crawler);
    // }

    // /**
    //  * Create a new user
    //  */
    // public function testCreate()
    // {
    //     $client = $this->connection();
    //     $crawler = $client->request('GET', '/user/new');
    //     $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

    //     // Vérifie si la page affiche le bon texte
    //     $this->assertContains(
    //             'Enregistrer',
    //             $client->getResponse()->getContent()
    //     );

    //     // Select the form and fill its values
    //     $form = $crawler->selectButton(' Créer')->form();
    //     $values = $form->getPhpValues();
    //     $values['app_user']['username'] = 'Jean';
    //     $values['app_user']['plainPassword']['first'] = 'motdepasse';
    //     $values['app_user']['plainPassword']['second'] = 'motdepasse';

    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $crawler = $client->followRedirect();
    //     $this->assertContains(
    //             'Jean',
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Try to create a new user with the same username as before
    //  */
    // public function testCreateFalse()
    // {
    //     $client = $this->connection();
    //     $crawler = $client->request('GET', '/user/new');
    //     $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

    //     // Vérifie si la page affiche le bon texte
    //     $this->assertContains(
    //             'Enregistrer',
    //             $client->getResponse()->getContent()
    //     );

    //     // Select the form and fill its values
    //     $form = $crawler->selectButton(' Créer')->form();
    //     $values = $form->getPhpValues();
    //     $values['app_user']['username'] = 'Jean';
    //     $values['app_user']['plainPassword']['first'] = 'motdepasse';
    //     $values['app_user']['plainPassword']['second'] = 'motdepasse';

    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains(
    //             'n\'a pas pu être créé.e',
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Change the responsibility of the user created for the test
    //  * Affects the responsibility Gestionnaire 1
    //  */
    // public function testEditResponsibility()
    // {
    //     $editPage = $this->accessEditPage();
    //     $client = $editPage[0];
    //     $crawler = $editPage[1];

    //     $form = $crawler->selectButton('Changer les informations')->form();
    //     $form['app_user']['responsibilities'][1]->tick();
    //     $crawler = $client->submit($form);
    //     $crawler = $client->followRedirect();
    //     $this->assertContains('Les informations ont bien été modifiées',
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Change the username
    //  */
    // public function testEditPseudo()
    // {
    //     $editPage = $this->accessEditPage();
    //     $client = $editPage[0];
    //     $crawler = $editPage[1];

    //     $form = $crawler->selectButton('Changer les informations')->form();
    //     $values = $form->getPhpValues();
    //     $values['app_user']['username'] = 'René';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $crawler = $client->followRedirect();
    //     $this->assertContains('René',
    //             $client->getResponse()->getContent()
    //     );
    //     $this->assertContains('Les informations ont bien été modifiées',
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Change the username to something that already exists
    //  * Assumes admin already exists (based on other tests)
    //  */
    // public function testEditPseudoFalse()
    // {
    //     $editPage = $this->accessEditPage();
    //     $client = $editPage[0];
    //     $crawler = $editPage[1];

    //     $form = $crawler->selectButton('Changer les informations')->form();
    //     $values = $form->getPhpValues();
    //     $values['app_user']['username'] = 'admin';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains("est pas disponible.",
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Change the password
    //  */
    // public function testEditPassword()
    // {
    //     $editPage = $this->accessEditPage();
    //     $client = $editPage[0];
    //     $crawler = $editPage[1];

    //     $form = $crawler->selectButton('Changer le mot de passe')->form();
    //     $values = $form->getPhpValues();
    //     $values['app_password']['oldPassword'] = 'motdepasse';
    //     $values['app_password']['plainPassword']['first'] = 'password';
    //     $values['app_password']['plainPassword']['second'] = 'password';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains('Le mot de passe a bien été modifié',
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Try to change the password
    //  */
    // public function testEditPasswordFalse()
    // {
    //     $editPage = $this->accessEditPage();
    //     $client = $editPage[0];
    //     $crawler = $editPage[1];

    //     $form = $crawler->selectButton('Changer le mot de passe')->form();
    //     $values = $form->getPhpValues();

    //     // oldPassword doesnt correspond
    //     $values['app_password']['oldPassword'] = 'motdepasse';
    //     $values['app_password']['plainPassword']['first'] = 'password';
    //     $values['app_password']['plainPassword']['second'] = 'password';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains('mot de passe ne correspond pas',
    //             $client->getResponse()->getContent()
    //     );

    //     // Values of plain password dont correspond
    //     $values['app_password']['oldPassword'] = 'password';
    //     $values['app_password']['plainPassword']['first'] = 'kldfh';
    //     $values['app_password']['plainPassword']['second'] = 'qsdqsd';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains('Les mots de passe doivent être identiques',
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Delete the user created for the test
    //  */
    // public function testDelete()
    // {
    //    $editPage = $this->accessEditPage();
    //    $client = $editPage[0];
    //    $crawler = $editPage[1];

    //    $form = $crawler->selectButton('delete_button')->form();
    //    $crawler = $client->submit($form);
    //    $crawler = $client->followRedirect();
    //    $this->assertContains('Liste des utilisateurices',
    //            $client->getResponse()->getContent()
    //    );
    // }

    // /**
    //  * Test everything at once
    //  * Delete from another
    //  */
    // public function testAll()
    // {
    //     $this->testCreate();
    //     $this->testEditPseudo();
    //     $this->testEditResponsability();
    //     $this->testEditPassword();

    //     $client = $this->connection();
    //     $crawler = $client->request('GET', '/user/');
    //     $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    //     $this->assertContains(
    //             'Liste des utilisateurices',
    //             $client->getResponse()->getContent()
    //     );
    //     // Select the button of the user created for the test
    //     // Wont work if there are already more than 10 users in the database
    //     // Only works for me with 5 other users in the database
    //     // The id is a link, the delete, show & edit buttons are links
    //     $link = $crawler
    //         ->filter('tr > td > a:contains("")')
    //         ->eq(22)
    //         ->link()
    //     ;
    //     $crawler = $client->click($link);
    //     $this->assertContains('Profil de René',
    //             $client->getResponse()->getContent()
    //     );
    //     $form = $crawler->selectButton('delete_button')->form();
    //     $crawler = $client->submit($form);
    //     $crawler = $client->followRedirect();
    //     $this->assertContains('Liste des utilisateurices',
    //             $client->getResponse()->getContent()
    //     );
    // }

    // /**
    //  * Delete from link in the list
    //  */
    // public function testDeleteFromList()
    // {
    //     $this->testCreate();

    //     $client = $this->connection();
    //     $crawler = $client->request('GET', '/user/');
    //     $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    //     $this->assertContains(
    //             'Liste des utilisateurices',
    //             $client->getResponse()->getContent()
    //     );
    //     // Select the button of the user created for the test
    //     // Wont work if there are already more than 10 users in the database
    //     // Only works for me with 5 other users in the database
    //     // The id is a link, the show & edit buttons are links
    //     $link = $crawler
    //         ->filter('tr > td > a:contains("")')
    //         ->eq(21)
    //         ->link()
    //     ;
    //     $crawler = $client->click($link);

    //     $form = $crawler->selectButton('delete_button')->form();
    //     $crawler = $client->submit($form);
    //     $crawler = $client->followRedirect();
    //     $this->assertContains('Liste des utilisateurices',
    //             $client->getResponse()->getContent()
    //     );
    // }
}

?>
