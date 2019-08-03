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
    const FIREWALL_NAME = 'main';
    const FIREWALL_CONTEXT = 'main';

    const ADMIN_USERNAME = 'admin';
    const GESTIONNAIRE_USERNAME = 'gest1';

    static $client;
    static $container;
    static $session;

    public function setUp()
    {
        self::$client = static::createClient();
        self::$container = static::$kernel->getContainer();
        self::$session = self::$container->get('session');
    }

    /*****************************/
    /* ~~~~ Utility methods ~~~~ */
    /*****************************/

    /**
     * Connect a user to the website
     *
     * @param string $username username of the user to connect (default: admin)
     * @return User the connected user
     */
    public function connection($username = self::ADMIN_USERNAME)
    {
        // Get the user we want to connect with
        $currentUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username
            ]);

        // Set the session
        $token = new UsernamePasswordToken($currentUser, null, self::FIREWALL_NAME, $currentUser->getRoles());
        self::$session->set('_security_' . self::FIREWALL_CONTEXT, serialize($token));
        self::$session->save();

        // Set the cookie
        self::$client->getCookieJar()->set(
                new Cookie(self::$session->getName(), self::$session->getId())
        );

        // Return the connected user
        return $currentUser;
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
    /*public function testCreateFalse()
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
    }*/

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
    // public function testEditPassword()
    // {
    //     $userAdminEditPage = $this->accessUserAdminEditPage();
    //     $client = $userAdminEditPage['client'];
    //     $crawler = $userAdminEditPage['crawler'];
    //
    //     $form = $crawler->selectButton('Changer le mot de passe')->form();
    //     $values = $form->getPhpValues();
    //     $values['app_password']['oldPassword'] = 'a';
    //     $values['app_password']['plainPassword']['first'] = 'password';
    //     $values['app_password']['plainPassword']['second'] = 'password';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains('Le mot de passe a bien été modifié',
    //             $client->getResponse()->getContent()
    //     );
    // }

    /**
     * Try to change the password
     */
    // public function testEditPasswordFalse()
    // {
    //     $userAdminEditPage = $this->accessUserAdminEditPage();
    //     $client = $userAdminEditPage['client'];
    //     $crawler = $userAdminEditPage['crawler'];
    //
    //     $form = $crawler->selectButton('Changer le mot de passe')->form();
    //     $values = $form->getPhpValues();
    //
    //     // oldPassword doesnt correspond
    //     $values['app_password']['oldPassword'] = 'motdepasse';
    //     $values['app_password']['plainPassword']['first'] = 'password';
    //     $values['app_password']['plainPassword']['second'] = 'password';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains('mot de passe ne correspond pas',
    //             $client->getResponse()->getContent()
    //     );
    //
    //     // Values of plain password dont correspond
    //     $values['app_password']['oldPassword'] = 'password';
    //     $values['app_password']['plainPassword']['first'] = 'kldfh';
    //     $values['app_password']['plainPassword']['second'] = 'qsdqsd';
    //     $crawler = $client->request($form->getMethod(), $form->getUri(), $values,$form->getPhpFiles());
    //     $this->assertContains('Les mots de passe doivent être identiques',
    //             $client->getResponse()->getContent()
    //     );
    // }

//  --------------------------------------------------------
//   Test the deletion of a user profile from the user list
//  --------------------------------------------------------
    /**
     * @group delete
     */
    public function testAdminDeleteAdminProfileFromEditPage()
    {
        // Connect the admin
        $admin = $this->connection(self::ADMIN_USERNAME);

        // Get the user which will be deleted
        $waitingDeletionUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => self::ADMIN_USERNAME
            ]);


        // Go to their profile page
        $editProfilePageUrl = '/user/' . $waitingDeletionUser->getId() . '/edit';
        $crawler = self::$client->request('GET', $editProfilePageUrl);
        $this->assertEquals(
                'Éditer le profil de ' . $waitingDeletionUser->getUsername(),
                $crawler->filter('h1')->first()->text(),
                'The page should be the admin edition one'
        );

        // Delete the profile using the button on the page
        $form = $crawler->selectButton('delete_button')->form();
        self::$client->submit($form);
        self::$session->set('_security_' . self::FIREWALL_CONTEXT, serialize(null));
        self::$session->invalidate();

        // Autoredirection to the user list
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/">/user/</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the user list'
        );
        self::$client->followRedirect();

        // Autoredirection to the login page because the user profile have been deleted
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="http://localhost/login">http://localhost/login</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the login page'
        );
        self::$client->followRedirect(); // redirect to login page
        $this->assertFalse(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Pour accéder au logiciel, identifiez-vous',
                self::$client->getResponse()->getContent(),
                'The page should be the login one'
        );
    }

    /**
     * @group delete
     */
    public function testAdminDeleteOtherUserProfileFromEditPage()
    {
        // Connect the admin
        $admin = $this->connection(self::ADMIN_USERNAME);

        // Get the user which will be deleted
        $waitingDeletionUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => self::GESTIONNAIRE_USERNAME
            ]);

        $editProfilePageUrl = '/user/' . $waitingDeletionUser->getId() . '/edit';

        // Go to their profile page
        $crawler = self::$client->request('GET', $editProfilePageUrl);
        $this->assertEquals(
                'Édition de l\'utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user edition one'
        );
        $this->assertContains(
                'Éditer le profil de ' . $waitingDeletionUser->getUsername(),
                self::$client->getCrawler()->filter('.breadcrumb > li')->last()->text(),
                'The page should be the gest1 edition one'
        );

        // Delete the profile using the button on the page
        $form = $crawler->selectButton('delete_button')->form();
        self::$client->submit($form);

        // Autoredirection to the user list
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/">/user/</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the user list'
        );
        self::$client->followRedirect();

        // Verification of the user list
        $this->assertEquals(
                'Liste des utilisateurices',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user list'
        );
        $this->assertContains(
                'L\'utilisateurice ' . $waitingDeletionUser->getUsername() . ' a bien été supprimé.e.',
                self::$client->getCrawler()->filter('.alert.alert-success')->first()->text(),
                'A success message should be displayed'
        );
        $this->assertNotContains(
                'L\'utilisateurice ' . $waitingDeletionUser->getUsername() . ' a bien été supprimé.e.',
                self::$client->getCrawler()->filter('table')->html(),
                'The user shouldn\'t appear on the list'
        );

        // Check if the user is well deleted in the database
        $deletedUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => self::GESTIONNAIRE_USERNAME
            ]);
        $this->assertEquals($deletedUser, null, 'The user should have been deleted');
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

