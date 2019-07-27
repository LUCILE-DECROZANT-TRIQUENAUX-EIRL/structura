<?php
namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

use Symfony\Component\HttpFoundation\File\Exception\NoFileException;

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
        $currentUser = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => 'admin'
            ]);

        $token = new UsernamePasswordToken($currentUser, null, 'main', $currentUser->getRoles());

        // Set the session
        $session->set('_security_main', serialize($token));
        $session->save();

        // Set the cookie
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        // Return the client
        return [
            'client' => $client,
            'currentUser' => $currentUser
        ];
    }

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the user list page.
     */
    public function accessUserListPage()
    {
        $connection = $this->connection();
        $client = $connection['client'];
        $crawler = $client->request('GET', '/user/');

        return [
            'client' => $client,
            'crawler' => $crawler
        ];
    }

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the admin user edit page.
     */
    public function accessUserAdminEditPage()
    {
        $connection = $this->connection();
        $client = $connection['client'];
        $currentUser = $connection['currentUser'];

        $userAdminEditPageUrl = '/user/' . $currentUser->getId() . '/edit';

        $crawler = $client->request('GET', $userAdminEditPageUrl);

        return [
            'client' => $client,
            'crawler' => $crawler
        ];
    }

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the admin user edit page.
     */
    public function accessUserAdherentEditPage()
    {
        $connection = $this->connection();
        $client = $connection['client'];

        // Get the adhe5 user
        $adherent = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => 'adhe5'
            ]);

        $userAdherentEditPageUrl = '/user/' . $adherent->getId() . '/edit';

        $crawler = $client->request('GET', $userAdherentEditPageUrl);

        return [
            'client' => $client,
            'crawler' => $crawler
        ];
    }

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the user edit page.
     */
    public function accessUserCreationPage()
    {
        $connection = $this->connection();
        $client = $connection['client'];
        $currentUser = $connection['currentUser'];

        $crawler = $client->request('GET', '/user/new');

        return [
            'client' => $client,
            'crawler' => $crawler
        ];
    }

    /*****************************/
    /* ~~~~~ Test methods ~~~~~~ */
    /*****************************/

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the user creation page.
     */
    public function testAccessUserCreationPage()
    {
        $userCreationPage = $this->accessUserCreationPage();
        $client = $userCreationPage['client'];

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        // Vérifie si la page affiche le bon texte
        $this->assertContains(
                'Enregistrer',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the user list page.
     */
    public function testAccessUserListPage()
    {
        $userListPage = $this->accessUserListPage();
        $client = $userListPage['client'];

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains(
                'Liste des utilisateurices',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Returns a client object and a crawler object.
     * The "user" is connected and on the edit page.
     */
    public function testAccessEditPage()
    {
        $userAdminEditPage = $this->accessUserAdminEditPage();
        $client = $userAdminEditPage['client'];

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains(
                'Édition de',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Create a new user
     */
    public function testCreate()
    {
        $userCreationPage = $this->accessUserCreationPage();
        $client = $userCreationPage['client'];
        $crawler = $userCreationPage['crawler'];

        // Select the form and fill its values
        $form = $crawler->selectButton(' Créer')->form();
        //var_dump(['app_user']['responsibilities'][2]);
        $form['app_user']['responsibilities'][2]->tick();
        $values = $form->getPhpValues();

        $values['app_user']['username'] = 'Jean';
        $values['app_user']['plainPassword']['first'] = 'motdepasse';
        $values['app_user']['plainPassword']['second'] = 'motdepasse';

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $crawler = $client->followRedirect();
        $this->assertContains(
                'Jean',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Try to create a new user with the an already taken username
     */
    public function testCreateFalse()
    {
        $userCreationPage = $this->accessUserCreationPage();
        $client = $userCreationPage['client'];
        $crawler = $userCreationPage['crawler'];

        // Select the form and fill its values
        $form = $crawler->selectButton(' Créer')->form();
        $values = $form->getPhpValues();
        $values['app_user']['username'] = 'admin';
        $values['app_user']['plainPassword']['first'] = 'motdepasse';
        $values['app_user']['plainPassword']['second'] = 'motdepasse';

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());

        $this->assertContains(
                'n\'a pas pu être créé.e',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Change the responsibility of the user created for the test
     * Affects the responsibility Gestionnaire 1
     */
    public function testEditResponsibility()
    {
        $userAdminEditPage = $this->accessUserAdminEditPage();
        $client = $userAdminEditPage['client'];
        $crawler = $userAdminEditPage['crawler'];

        $form = $crawler->selectButton('Changer les informations')->form();
        $form['app_user']['responsibilities'][3]->tick();
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertContains('Les informations ont bien été modifiées',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Change the username
     */
    public function testEditPseudo()
    {
        $userAdminEditPage = $this->accessUserAdminEditPage();
        $client = $userAdminEditPage['client'];
        $crawler = $userAdminEditPage['crawler'];

        $form = $crawler->selectButton('Changer les informations')->form();
        $values = $form->getPhpValues();
        $values['app_user']['username'] = 'René';
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
     * Change the username to something that already exists
     */
    public function testEditPseudoFalse()
    {
        $userAdminEditPage = $this->accessUserAdminEditPage();
        $client = $userAdminEditPage['client'];
        $crawler = $userAdminEditPage['crawler'];

        $form = $crawler->selectButton('Changer les informations')->form();
        $values = $form->getPhpValues();
        $values['app_user']['username'] = 'info';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());

        $this->assertContains("est pas disponible.",
                $client->getResponse()->getContent()
        );
    }

    /**
     * Change the password
     */
    public function testEditPassword()
    {
        $userAdminEditPage = $this->accessUserAdminEditPage();
        $client = $userAdminEditPage['client'];
        $crawler = $userAdminEditPage['crawler'];

        $form = $crawler->selectButton('Changer le mot de passe')->form();
        $values = $form->getPhpValues();
        $values['app_password']['oldPassword'] = 'a';
        $values['app_password']['plainPassword']['first'] = 'password';
        $values['app_password']['plainPassword']['second'] = 'password';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $this->assertContains('Le mot de passe a bien été modifié',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Try to change the password
     */
    public function testEditPasswordFalse()
    {
        $userAdminEditPage = $this->accessUserAdminEditPage();
        $client = $userAdminEditPage['client'];
        $crawler = $userAdminEditPage['crawler'];

        $form = $crawler->selectButton('Changer le mot de passe')->form();
        $values = $form->getPhpValues();

        // oldPassword doesnt correspond
        $values['app_password']['oldPassword'] = 'motdepasse';
        $values['app_password']['plainPassword']['first'] = 'password';
        $values['app_password']['plainPassword']['second'] = 'password';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $this->assertContains('mot de passe ne correspond pas',
                $client->getResponse()->getContent()
        );

        // Values of plain password dont correspond
        $values['app_password']['oldPassword'] = 'password';
        $values['app_password']['plainPassword']['first'] = 'kldfh';
        $values['app_password']['plainPassword']['second'] = 'qsdqsd';
        $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
        $this->assertContains('Les mots de passe doivent être identiques',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Delete the user created for the test
     */
    public function testDelete()
    {
       $userAdherentEditPage = $this->accessUserAdherentEditPage();
       $client = $userAdherentEditPage['client'];
       $crawler = $userAdherentEditPage['crawler'];

       $form = $crawler->selectButton('delete_button')->form();
       $crawler = $client->submit($form);
       $crawler = $client->followRedirect();
       $this->assertContains('Liste des utilisateurices',
               $client->getResponse()->getContent()
       );
    }

    /**
     * Test everything at once
     * Delete from another
     */
    // public function testAll()
    // {
    //     $this->create();
    //     $this->editResponsibility();
    //     $this->editPseudo();
    //     //$this->editResponsibility();
    //     $this->editPassword();

    //     $connection = $this->connection();
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

    //     $connection = $this->connection();
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
