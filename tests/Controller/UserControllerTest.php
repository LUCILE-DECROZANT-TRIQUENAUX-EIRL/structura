<?php
namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Responsibility;

use Symfony\Component\HttpFoundation\File\Exception\NoFileException;

class UserControllerTest extends WebTestCase
{
    const FIREWALL_NAME = 'main';
    const FIREWALL_CONTEXT = 'main';

    const ADMIN_USERNAME = 'admin';
    const ADMIN_ONLY_USERNAME = 'adminUniquement';
    const GESTIONNAIRE_USERNAME = 'gest1';
    const INFORMATEURICE_USERNAME = 'info';
    const ADHERENTE_USERNAME = 'adhe1';
    const SYMPATHISANTE_USERNAME = 'supp1';
    const MECENE_USERNAME = 'rich1';
    const INSCRITE_USERNAME = 'inscr';
    const RANDOM_USER_USERNAME = 'adhe5';

    static $client;
    static $container;
    static $session;

    public function setUp()
    {
        self::$client = static::createClient();
        self::$container = static::$kernel->getContainer();
        self::$session = self::$container->get('session');
    }

    // *****************************
    // * ~~~~ Utility methods ~~~~ *
    // *****************************

    /**
     * Connect a user to the website
     *
     * @param string $username username of the user to connect (default: admin)
     * @return User the connected user
     */
    private function connection($username = self::ADMIN_USERNAME)
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

    // *****************************
    // * ~~~~~ Test methods ~~~~~~ *
    // *****************************

//  -------------------------------------------------
//   Test the access of the user create profile page
//  -------------------------------------------------
    /**
     * @group access
     */
    public function testAdminAccessCreateUserProfilePage()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user edition one'
        );

        // Connect the admin
        $this->connection(self::ADMIN_ONLY_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user edition one'
        );
    }

    /**
     * @group access
     */
    public function testGestionnaireAccessCreateUserProfilePage()
    {
        // Connect the gestionnaire
        $this->connection(self::GESTIONNAIRE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testInformateuriceAccessCreateUserProfilePage()
    {
        // Connect the informateurice
        $this->connection(self::INFORMATEURICE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testAdherenteAccessCreateUserProfilePage()
    {
        // Connect the adherent.e
        $this->connection(self::ADHERENTE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testSympathisanteAccessCreateUserProfilePage()
    {
        // Connect the adherent.e
        $this->connection(self::SYMPATHISANTE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testMeceneAccessCreateUserProfilePage()
    {
        // Connect the adherent.e
        $this->connection(self::MECENE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testInscriteAccessCreateUserProfilePage()
    {
        // Connect the adherent.e
        $this->connection(self::INSCRITE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

//  -----------------------------
//   Test the creation of a user
//  -----------------------------

    /**
     * @group create
     */
    public function testCreateUserRequiredFields()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
            ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/18">/user/18</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                'username-test',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                1,
                'The user should only have one responsibility'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserAdmin()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the Administrateurice responsibility data from database
        $responsibilityAdmin = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::ADMINISTRATEURICE_LABEL,
            ]);
        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
                'app_user[responsibilities]' => [
                    $responsibilityAdmin->getId(),
                ],
            ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/'. $createdUser->getId() . '">/user/'. $createdUser->getId() . '</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                $username,
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityAdmin),
                'The user should have the Administrateurice responsibility'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                2,
                'The user should only have two responsibilities'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserAdminSensible()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the Administrateurice responsibility data from database
        $responsibilityAdmin = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::ADMINISTRATEURICE_LABEL,
            ]);
        // Get the Administrateurice sensible responsibility data from database
        $responsibilitySensibleAdmin = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::ADMINISTRATEURICE_SENSIBLE_LABEL,
            ]);
        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
                'app_user[responsibilities]' => [
                    $responsibilityAdmin->getId(),
                    $responsibilitySensibleAdmin->getId(),
                ],
            ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/'. $createdUser->getId() . '">/user/'. $createdUser->getId() . '</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                $username,
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityAdmin),
                'The user should have the Administrateurice responsibility'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilitySensibleAdmin),
                'The user should have the Administrateurice responsibility'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                3,
                'The user should only have three responsibilities'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserGestionnaire()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the Gestionnaire responsibility data from database
        $responsibilityManager = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::GESTIONNAIRE_LABEL,
            ]);
        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
                'app_user[responsibilities]' => [
                    $responsibilityManager->getId(),
            ],
        ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/'. $createdUser->getId() . '">/user/'. $createdUser->getId() . '</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                $username,
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityManager),
                'The user should have the Gestionnaire responsibility'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                2,
                'The user should only have two responsibilities'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserGestionnaireSensible()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the Gestionnaire responsibility data from database
        $responsibilityManager = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::GESTIONNAIRE_LABEL,
            ]);
        // Get the Gestionnaire sensible responsibility data from database
        $responsibilitySensibleManager = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::GESTIONNAIRE_SENSIBLE_LABEL,
            ]);
        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
                'app_user[responsibilities]' => [
                    $responsibilityManager->getId(),
                    $responsibilitySensibleManager->getId(),
                ],
            ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/'. $createdUser->getId() . '">/user/'. $createdUser->getId() . '</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                $username,
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityManager),
                'The user should have the Gestionnaire responsibility'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilitySensibleManager),
                'The user should have the Gestionnaire sensible responsibility'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                3,
                'The user should only have three responsibilities'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserInformateurice()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the Informateurice responsibility data from database
        $responsibilityInformation = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::INFORMATEURICE_LABEL,
            ]);
        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
                'app_user[responsibilities]' => [
                    $responsibilityInformation->getId(),
                ],
            ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/'. $createdUser->getId() . '">/user/'. $createdUser->getId() . '</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                $username,
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInformation),
                'The user should have the Informateurice responsibility'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                2,
                'The user should only have two responsibilities'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserSympathisante()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the Sympathisant.e responsibility data from database
        $responsibilitySympathize = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::SYMPATHIZE_LABEL,
            ]);
        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
                'app_user[responsibilities]' => [
                    $responsibilitySympathize->getId(),
                ],
            ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/'. $createdUser->getId() . '">/user/'. $createdUser->getId() . '</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                $username,
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilitySympathize),
                'The user should have the Sympathisant.e responsibility'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                2,
                'The user should only have two responsibilities'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserAnnuaire()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the Consultation annuaire responsibility data from database
        $responsibilityDoctorsBook = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::CONSULTATION_ANNUAIRE_LABEL,
            ]);
        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
                'app_user[responsibilities]' => [
                    $responsibilityDoctorsBook->getId(),
                ],
            ]);

        // Submit the form
        self::$client->submit($form);

        // Autoredirection to the user list
        $createdUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => $username,
            ]);
        $this->assertTrue(self::$client->getResponse()->isRedirection());
        $this->assertContains(
                'Redirecting to <a href="/user/'. $createdUser->getId() . '">/user/'. $createdUser->getId() . '</a>.',
                self::$client->getResponse()->getContent(),
                'The page should be redirecting to the newly created user profile one'
        );
        self::$client->followRedirect();
        $this->assertEquals(
                $username,
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the newly created user profile one'
        );

        // Check the database content
        $responsibilityInscrite = self::$container
            ->get('doctrine')
            ->getRepository(Responsibility::class)
            ->findOneBy([
                'label' => Responsibility::REGISTERED_LABEL,
            ]);
        $this->assertEquals(
                $createdUser->getUsername(),
                $username,
                'The username should be ' . $username
        );
        $this->assertTrue(
                password_verify($password, $createdUser->getPassword()),
                'The passwords should correspond'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityInscrite),
                'The user should have the Inscrit.e responsibility by default'
        );
        $this->assertTrue(
                $createdUser->hasResponsibility($responsibilityDoctorsBook),
                'The user should have the Consultation de l\'annuaire responsibility'
        );
        $this->assertEquals(
                count($createdUser->getResponsibilities()),
                2,
                'The user should only have two responsibilities'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserUsernameAlreadyTaken()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the count of users in the database
        $userCountBeforeTest = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->count([]);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = self::ADMIN_USERNAME;
        $password = 'a';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password,
                'app_user[plainPassword][second]' => $password,
            ]);

        // Submit the form
        self::$client->submit($form);

        // Get the count of users in the database after the test
        $userCountAfterTest = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->count([]);

        // The form throws an error
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );
        $this->assertContains(
                'L\'utilisateurice ' . $username . ' n\'a pas pu être créé.e',
                self::$client->getCrawler()->filter('.alert.alert-danger')->first()->text(),
                'An error message should be displayed'
        );
        $this->assertContains(
                'Ce nom d\'utilisateurice n\'est pas disponible.',
                self::$client->getCrawler()->filter('.invalid-feedback')->first()->text(),
                'An error message should be displayed'
        );
        // Check of the database content
        $this->assertEquals(
                $userCountBeforeTest,
                $userCountAfterTest,
                'The user count shouldn\'t have changed'
        );
    }

    /**
     * @group create
     */
    public function testCreateUserPasswordsNotTheSame()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the count of users in the database
        $userCountBeforeTest = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->count([]);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Select the form
        $form = self::$client->getCrawler()->selectButton('create-user-submit-button')->form();
        // Fill the form inputs
        $username = 'username-test';
        $password1 = 'a';
        $password2 = 'b';
        $form->disableValidation()
            ->setValues([
                'app_user[username]' => $username,
                'app_user[plainPassword][first]' => $password1,
                'app_user[plainPassword][second]' => $password2,
            ]);

        // Submit the form
        self::$client->submit($form);

        // Get the count of users in the database after the test
        $userCountAfterTest = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->count([]);

        // The form throws an error
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );
        $this->assertContains(
                'L\'utilisateurice ' . $username . ' n\'a pas pu être créé.e',
                self::$client->getCrawler()->filter('.alert.alert-danger')->first()->text(),
                'An error message should be displayed'
        );
        $this->assertContains(
                'Les mots de passe doivent être identiques',
                self::$client->getCrawler()->filter('.invalid-feedback')->first()->text(),
                'An error message should be displayed'
        );
        // Check of the database content
        $this->assertEquals(
                $userCountBeforeTest,
                $userCountAfterTest,
                'The user count shouldn\'t have changed'
        );
    }

//  -------------------------------------------------
//   Test the navigation elements on the create page
//  -------------------------------------------------

    /**
     * @group navigation
     */
    public function testReturnButtonOnCreatePage()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get the count of users in the database before the test
        $userCountBeforeTest = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->count([]);

        // Go to the profile creation page
        self::$client->request('GET', '/user/new');
        $this->assertEquals(
                'Enregistrer un.e nouvel.le utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user creation one'
        );

        // Get the return to the list button and use it
        $returnButton = self::$client->getCrawler()
                // we can't use the whole label as the crawler is not able
                // to interpret correctly the non breaking spaces
                ->selectLink('à la liste des utilisateurices')
                ->link();
        self::$client->request('GET', $returnButton->getUri());

        // Check that we are on the user list
        $this->assertEquals(
                'Liste des utilisateurices',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user list'
        );

        // Get the count of users in the database after the test
        $userCountAfterTest = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->count([]);
        // Check the database content
        $this->assertEquals(
                $userCountBeforeTest,
                $userCountAfterTest,
                'The user count shouldn\'t have changed'
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

//  ---------------------------------------
//   Test the access of the user list page
//  ---------------------------------------
    /**
     * @group access
     */
    public function testAdminAccessUserListPage()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Go to the user list page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                'Liste des utilisateurices',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user edition one'
        );

        // Connect the admin
        $this->connection(self::ADMIN_ONLY_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                'Liste des utilisateurices',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user edition one'
        );
    }

    /**
     * @group access
     */
    public function testGestionnaireAccessUserListPage()
    {
        // Connect the gestionnaire
        $this->connection(self::GESTIONNAIRE_USERNAME);

        // Go to the user list page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testInformateuriceAccessUserListPage()
    {
        // Connect the informateurice
        $this->connection(self::INFORMATEURICE_USERNAME);

        // Go to the user list page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testAdherenteAccessUserListPage()
    {
        // Connect the adherent.e
        $this->connection(self::ADHERENTE_USERNAME);

        // Go to the user list page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testSympathisanteAccessUserListPage()
    {
        // Connect the adherent.e
        $this->connection(self::SYMPATHISANTE_USERNAME);

        // Go to the user list page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testMeceneAccessUserListPage()
    {
        // Connect the adherent.e
        $this->connection(self::MECENE_USERNAME);

        // Go to the user list page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testInscriteAccessUserListPage()
    {
        // Connect the adherent.e
        $this->connection(self::INSCRITE_USERNAME);

        // Go to the user list page
        self::$client->request('GET', '/user/');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

//  -----------------------------------------------
//   Test the access of the user edit profile page
//  -----------------------------------------------
    /**
     * @group access
     */
    public function testAdminAccessEditUserProfilePage()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

        // Get a user to access their edit profile page
        $waitingDeletionUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => self::RANDOM_USER_USERNAME
            ]);


        // Go to their profile page
        $editProfilePageUrl = '/user/' . $waitingDeletionUser->getId() . '/edit';
        self::$client->request('GET', $editProfilePageUrl);
        $this->assertEquals(
                'Édition de l\'utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user edition one'
        );
        $this->assertContains(
                'Éditer le profil de ' . $waitingDeletionUser->getUsername(),
                self::$client->getCrawler()->filter('.breadcrumb > li')->last()->text(),
                'The page should be the random user edition one'
        );

        // Connect the admin
        $this->connection(self::ADMIN_ONLY_USERNAME);

        // Go to the profile page
        $editProfilePageUrl = '/user/' . $waitingDeletionUser->getId() . '/edit';
        self::$client->request('GET', $editProfilePageUrl);
        $this->assertEquals(
                'Édition de l\'utilisateurice',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the user edition one'
        );
        $this->assertContains(
                'Éditer le profil de ' . $waitingDeletionUser->getUsername(),
                self::$client->getCrawler()->filter('.breadcrumb > li')->last()->text(),
                'The page should be the random user edition one'
        );
    }

    /**
     * @group access
     */
    public function testGestionnaireAccessEditUserProfilePage()
    {
        // Connect the gestionnaire
        $this->connection(self::GESTIONNAIRE_USERNAME);

        // Get a user to access their edit profile page
        $waitingDeletionUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => self::RANDOM_USER_USERNAME
            ]);


        // Go to their profile page
        $editProfilePageUrl = '/user/' . $waitingDeletionUser->getId() . '/edit';
        self::$client->request('GET', $editProfilePageUrl);
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testInformateuriceAccessEditUserProfilePage()
    {
        // Connect the informateurice
        $this->connection(self::INFORMATEURICE_USERNAME);

        // Get a user to access their edit profile page
        $waitingDeletionUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => self::RANDOM_USER_USERNAME
            ]);


        // Go to their profile page
        $editProfilePageUrl = '/user/' . $waitingDeletionUser->getId() . '/edit';
        self::$client->request('GET', $editProfilePageUrl);
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * @group access
     */
    public function testAdherenteAccessEditUserProfilePage()
    {
        // Connect the adherent.e
        $this->connection(self::ADHERENTE_USERNAME);

        // Get a user to access their edit profile page
        $waitingDeletionUser = self::$container
            ->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy([
                'username' => self::RANDOM_USER_USERNAME
            ]);


        // Go to their profile page
        $editProfilePageUrl = '/user/' . $waitingDeletionUser->getId() . '/edit';
        self::$client->request('GET', $editProfilePageUrl);
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

//  --------------------------------------------------------
//   Test the deletion of a user profile from the user list
//  --------------------------------------------------------
    /**
     * @group delete
     */
    public function testAdminDeleteAdminProfileFromEditPage()
    {
        // Connect the admin
        $this->connection(self::ADMIN_USERNAME);

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
        $this->connection(self::ADMIN_USERNAME);

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

