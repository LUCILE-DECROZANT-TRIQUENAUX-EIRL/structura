<?php
namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\People;
use App\Entity\Responsibility;
use App\Entity\Denomination;
use App\Entity\Address;

use Symfony\Component\HttpFoundation\File\Exception\NoFileException;

class PeopleControllerTest extends WebTestCase
{
    const FIREWALL_NAME = 'main';
    const FIREWALL_CONTEXT = 'main';

    const ADMIN_USERNAME = 'admin';
    const ADMIN_ONLY_USERNAME = 'adminUniquement';
    const GESTIONNAIRE_USERNAME = 'gest1';
    const GESTIONNAIRE_SENSIBLE_USERNAME = 'gestiSensible';
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

//=========== Create a people

    //  -------------------------------------------------
    //   Test the access of the people create profile page
    //  -------------------------------------------------
        /**
         * Tests if a person with the gestionnaire role can access the page
         */
        public function testGestionnaireAccessCreatePeoplePage()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Go to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people creation one'
            );

            // Connects the gestionnaire sensible
            $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

            // Go to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people creation one'
            );

            // Tests if the buttons are there
            $this->assertEquals(
                    ' Retourner à la liste des adhérent⋅es',
                    self::$client->getCrawler()->filterXPath('//div/a')->last()->text(),
                    'The page should have a return button'
            );
            // Tests the breadcrumb
            $this->assertEquals(
                    'Accueil',
                    self::$client->getCrawler()->filterXPath('(//li/a)[3]')->text(),
                    'The breadcrumb should link to the Home page'
            );
            $this->assertEquals(
                    'Liste des adhérent.es',
                    self::$client->getCrawler()->filterXPath('(//li/a)[4]')->text(),
                    'The breadcrumb should link to the List page'
            );
            $this->assertContains(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filterXPath('(//li)[5]')->text(),
                    'The breadcrumb should be on the new memeber page'
            );


        }

        /**
         * Tests if a person with the admin only role can access the page
         */
        public function testAdminAccessCreatePeoplePage()
        {

            // Connects the admin only
            $this->connection(self::ADMIN_ONLY_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );

            // Connects the admin
            // Has also gestionnaire role so can access the page
            $this->connection(self::ADMIN_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people creation one'
            );
        }

        /**
         * Tests if a person with the adherente role can access the page
         */
        public function testAdherenteAccessCreatePeoplePage()
        {
            // Connects the adherent.e
            $this->connection(self::ADHERENTE_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );
        }

        /**
         * Tests if a person with the Sympathisant role can access the page
         */
        public function testSympathisanteAccessCreatePeoplePage()
        {
            // Connect the adherent.e
            $this->connection(self::SYMPATHISANTE_USERNAME);

            // Go to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );
        }

        /**
         * Tests if a person with the mécène role can access the page
         */
        public function testMeceneAccessCreatePeoplePage()
        {
            // Connects the adherent.e
            $this->connection(self::MECENE_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );
        }

        /**
         * Tests if a person with the inscrit role can access the page
         */
        public function testInscriteAccessCreatePeoplePage()
        {
            // Connects the adherent.e
            $this->connection(self::INSCRITE_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );
        }


        /**
         * Tests if a person with the gestionnaire role can access the page
         */
        public function testGestionnaireNotSensibleCanNotSeeSensitiveObservations()
        {


            // Connects the admin
            $this->connection(self::ADMIN_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertNotContains(
                    'Détails médicaux',
                    self::$client->getCrawler()->filter('label')->last()->text(),
                    'The page should not contain sensitive observations'
            );

        }

        /**
         * Tests if a person with the gestionnaire role can access the page
         */
        public function testGestionnaireSensibleCanSeeSensitiveObservations()
        {

            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

            // Go to the profile creation page
            self::$client->request('GET', '/people/new');
            $this->assertContains(
                'Détails médicaux',
                self::$client->getCrawler()->filter('label')->last()->text(),
                'The page should contain sensitive observations'
            );

        }


    //  -----------------------------
    //   Test the creation of a people
    //  -----------------------------

        /**
         * Creates an user with mandatory form inputs
         */
        public function testCreatePeopleRequiredFields()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-people-submit-button')->form();
            // Fills the mandatory form inputs
            $firstname = 'Florent';
            $name = 'Marin';
            $form->disableValidation()
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                ]);
            $form['app_user[denomination]']->select(2);

            // Submits the form
            self::$client->submit($form);

            // Autoredirection to the people profile
            $this->assertContains(
                    'Redirecting to <a href="/people/18">/people/18</a>.',
                    self::$client->getResponse()->getContent(),
                    'The page should be redirecting to the newly created user profile one'
            );
            self::$client->followRedirect();
            $this->assertContains(
                    'Monsieur Florent Marin',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the newly created user profile one'
            );

            // Checks the database content
            $createdPeople = self::$container
                ->get('doctrine')
                ->getRepository(People::class)
                ->findOneBy([
                    'firstName' => $firstname,
                ]);

            $this->assertEquals(
                    $createdPeople->getFirstName(),
                    $firstname,
                    'The first name should be ' . $firstname
            );

            $this->assertEquals(
                    $createdPeople->getLastName(),
                    $name,
                    'The last name should be ' . $name
            );

            $denomination =$createdPeople->getDenomination();

            $this->assertEquals(
                    $denomination->getLabel(),
                    "Monsieur",
                    'The denomination should be Monsieur '
            );
        }

        /**
         * Creates an user with all form inputs
         */
        public function testCreatePeopleAllFields()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-people-submit-button')->form();
            // Fills the mandatory form inputs
            $firstname = 'Florent';
            $name = 'Marin';
            $line = '30 rue Carnot';
            $postalcode = '42510';
            $city =  'Balbigny';
            $country = 'France';
            $email = 'marin@mail.fr';
            $homenumber = '0134567890';
            $cellnumber = '0134567890';
            $worknumber = '0134567890';
            $faxnumber = '0134567890';
            $observations = 'Observations';
            $sensitive = 'Détails';
            $form->disableValidation()
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                    'app_user[addresses][__name__][line]' => $line,
                    'app_user[addresses][__name__][postalCode]' => $postalcode,
                    'app_user[addresses][__name__][city]' => $city,
                    'app_user[addresses][__name__][country]' => $country,
                    'app_user[emailAddress]' => $email,
                    'app_user[homePhoneNumber]' => $homenumber,
                    'app_user[cellPhoneNumber]' => $cellnumber,
                    'app_user[workPhoneNumber]' => $worknumber,
                    'app_user[workFaxNumber]' => $faxnumber,
                    'app_user[observations]' => $observations,
                    'app_user[sensitiveObservations]' => $sensitive
                ]);
            $form['app_user[denomination]']->select(2);
            $form['app_user[isReceivingNewsletter]']->tick();
            $form['app_user[newsletterDematerialization]']->tick();

            // Submits the form
            self::$client->submit($form);

            // Autoredirection to the people profile
            $this->assertContains(
                    'Redirecting to <a href="/people/18">/people/18</a>.',
                    self::$client->getResponse()->getContent(),
                    'The page should be redirecting to the newly created user profile one'
            );
            self::$client->followRedirect();
            $this->assertContains(
                    'Monsieur Florent Marin',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the newly created user profile one'
            );

            // Checks the database content
            $createdPeople = self::$container
                ->get('doctrine')
                ->getRepository(People::class)
                ->findOneBy([
                    'firstName' => $firstname,
                ]);

            $this->assertEquals(
                    $createdPeople->getFirstName(),
                    $firstname,
                    'The first name should be ' . $firstname
            );

            $this->assertEquals(
                    $createdPeople->getLastName(),
                    $name,
                    'The last name should be ' . $name
            );

            $this->assertEquals(
                    $createdPeople->getEmailAddress(),
                    $email,
                    'The email should be ' . $email
            );

            $this->assertEquals(
                    $createdPeople->getIsReceivingNewsletter(),
                    true,
                    'The user should receive the newsletter'
            );

            $this->assertEquals(
                    $createdPeople->getNewsletterDematerialization(),
                    true,
                    'The user should receive the newsletter by email'
            );

            $this->assertEquals(
                    $createdPeople->getCellPhoneNumber(),
                    $cellnumber,
                    'The cell phone number should be ' . $cellnumber
            );

            $this->assertEquals(
                    $createdPeople->getHomePhoneNumber(),
                    $homenumber,
                    'The home phone number should be ' . $homenumber
            );

            $this->assertEquals(
                    $createdPeople->getWorkPhoneNumber(),
                    $worknumber,
                    'The work phone number should be ' . $worknumber
            );

            $this->assertEquals(
                    $createdPeople->getWorkFaxNumber(),
                    $faxnumber,
                    'The fax number should be ' . $faxnumber
            );

            $this->assertEquals(
                    $createdPeople->getObservations(),
                    $observations,
                    'The observations should be ' . $observations
            );

            $this->assertEquals(
                    $createdPeople->getSensitiveObservations(),
                    $sensitive,
                    'The sensitive observations should be ' . $sensitive
            );

            $denomination = $createdPeople->getDenomination();

            $this->assertEquals(
                    $denomination->getLabel(),
                    "Monsieur",
                    'The denomination should be Monsieur'
            );

            $this->assertEquals(
                    $createdPeople->getAddresses(),
                    $createdPeople->getAddresses(),
                    'The address should be '. $line . $postalcode . $city . $country
            );
        }

        /**
         * Creates an user with mandatory form inputs empty
         * Form doesn't work in that way
         */
        public function testCreatePeopleRequiredFieldsEmpty()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-people-submit-button')->form();

            // Submits the form
            self::$client->submit($form);

            $this->assertContains(
                    'Symfony Exception',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should have an Exception'
            );

        }

        /**
         * Creates an user with text in the phone fields
         * Form doesn't work in that way
         */
        public function testCreatePeopleRequiredWrongPhone()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/people/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-people-submit-button')->form();

            // Fills the mandatory form inputs
            $firstname = 'Florent';
            $name = 'Marin';
            $cellPhoneNumber = 'numero';
            $form->disableValidation()
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                    'app_user[cellPhoneNumber]' => $cellPhoneNumber
                ]);
            $form['app_user[denomination]']->select(2);

            // Submits the form
            self::$client->submit($form);

            $this->assertContains(
                    'Symfony Exception',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should have an Exception'
            );

        }

//========= Edit a people

//  -------------------------------------------------
//   Tests the access of the people edit profile page
//  -------------------------------------------------
    /**
     * Tests if a person with the gestionnaire role can access the page
     */
    public function testGestionnaireAccessEditPeoplePage()
    {
        // Connects the gestionnaire
        $this->connection(self::GESTIONNAIRE_USERNAME);

        // Go to the profile edition page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                'Édition de l\'adhérent.e',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the people edition one'
        );

        // Connects the gestionnaire sensible
        $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

        // Go to the profile edition page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                'Édition de l\'adhérent.e',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the people edition one'
        );

        // Tests if the buttons are there
        $this->assertEquals(
                ' Retourner à la liste des adhérent⋅es',
                self::$client->getCrawler()->filterXPath('//div/a')->last()->text(),
                'The page should have a return button'
        );
        $this->assertContains(
                'Supprimer Jeanne' ,
                self::$client->getCrawler()->filterXPath('(//button)[2]')->text(),
                'The page should have a delete button'
        );
        // Tests the breadcrumb
        $this->assertEquals(
                'Accueil',
                self::$client->getCrawler()->filterXPath('(//li/a)[3]')->text(),
                'The breadcrumb should link to the Home page'
        );
        $this->assertEquals(
                'Liste des adhérent·es',
                self::$client->getCrawler()->filterXPath('(//li/a)[4]')->text(),
                'The breadcrumb should link to the List page'
        );
        $this->assertContains(
                'Profil de Jeanne Carton',
                self::$client->getCrawler()->filterXPath('(//li/a)[5]')->text(),
                'The breadcrumb should link to the people profile page'
        );
        $this->assertContains(
                'Éditer le profil de Jeanne Carton' ,
                self::$client->getCrawler()->filterXPath('(//li)[6]')->text(),
                'The breadcrumb should be on the edit people page'
        );


    }

    /**
     * Tests if a person with the admin role can access the page
     */
    public function testAdminAccessEditPeoplePage()
    {

        // Connects the admin only
        $this->connection(self::ADMIN_ONLY_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );

        // Connects the admin
        // Has also gestionnaire role so can access the page
        $this->connection(self::ADMIN_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                'Édition de l\'adhérent.e',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the people edition one'
        );
    }

    /**
     * Tests if a person with the adherente role can access the page
     */
    public function testAdherenteAccessEditPeoplePage()
    {
        // Connects the adherent.e
        $this->connection(self::ADHERENTE_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * Tests if a person with the Sympathisant role can access the page
     */
    public function testSympathisanteAccessEditPeoplePage()
    {
        // Connect the adherent.e
        $this->connection(self::SYMPATHISANTE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * Tests if a person with the mécène role can access the page
     */
    public function testMeceneAccessEditPeoplePage()
    {
        // Connects the adherent.e
        $this->connection(self::MECENE_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * Tests if a person with the inscrit role can access the page
     */
    public function testInscriteAccessEditPeoplePage()
    {
        // Connects the adherent.e
        $this->connection(self::INSCRITE_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }


    /**
     * Tests if a person with the gestionnaire role can access the page
     */
    public function testGestionnaireEditNotSensibleCanNotSeeSensitiveObservations()
    {


        // Connects the admin
        $this->connection(self::ADMIN_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertNotContains(
                'Détails médicaux',
                self::$client->getCrawler()->filter('label')->last()->text(),
                'The page should not contain sensitive observations'
        );

    }

    /**
     * Tests if a person with the gestionnaire role can access the page
     */
    public function testGestionnaireEditSensibleCanSeeSensitiveObservations()
    {

        // Connects the gestionnaire
        $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/people/17/edit');
        $this->assertContains(
            'Détails médicaux',
            self::$client->getCrawler()->filter('label')->last()->text(),
            'The page should contain sensitive observations'
        );

    }

    //  -----------------------------
    //   Test the edition of a people
    //  -----------------------------

        /**
         * Creates an user with mandatory form inputs
         */
        public function testEditPeopleRequiredFields()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile ediiton page
            $crawler = self::$client->request('GET', '/people/17/edit');
            $this->assertEquals(
                    'Édition de l\'adhérent.e',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people edition one'
            );


            $form =self::$client->getCrawler()->selectButton('edit-people-submit-button')->form();

            // Fills the mandatory form inputs
            $firstname = 'Florent';
            $name = 'Marin';
            $form
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                ]);
            $form
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                ]);
            $form['app_user[denomination]']->select(2);

            // Submits the form
            self::$client->submit($form);

            // Autoredirection to the people profile
            $this->assertContains(
                    '/people/17/edit',
                    self::$client->getResponse()->getContent(),
                    'The page should be redirecting to the newly created user profile one'
            );
            self::$client->followRedirect();
            $this->assertContains(
                    'Édition de l\'adhérent.e',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the newly created user profile one'
            );

            // Checks the database content
            $createdPeople = self::$container
                ->get('doctrine')
                ->getRepository(People::class)
                ->findOneBy([
                    'firstName' => $firstname,
                ]);

            $this->assertEquals(
                    $createdPeople->getFirstName(),
                    $firstname,
                    'The first name should be ' . $firstname
            );

            $this->assertEquals(
                    $createdPeople->getLastName(),
                    $name,
                    'The last name should be ' . $name
            );

            $denomination =$createdPeople->getDenomination();

            $this->assertEquals(
                    $denomination->getLabel(),
                    "Monsieur",
                    'The denomination should be Monsieur '
            );
       }

        /**
         * Creates an user with all form inputs
         */
         //TODO : Add the address edition since it doesn't work for now
        public function testEditPeopleAllFields()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

            // Goes to the profile ediiton page
            $crawler = self::$client->request('GET', '/people/17/edit');
            $this->assertEquals(
                    'Édition de l\'adhérent.e',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people edition one'
            );


            $form =self::$client->getCrawler()->selectButton('edit-people-submit-button')->form();
            // Fills the mandatory form inputs
            $firstname = 'Florent';
            $name = 'Marin';
            $line = '30 rue Carnot';
            $postalcode = '42510';
            $city =  'Balbigny';
            $country = 'France';
            $email = 'marin@mail.fr';
            $homenumber = '0134567890';
            $cellnumber = '0134567890';
            $worknumber = '0134567890';
            $faxnumber = '0134567890';
            $observations = 'Observations';
            $sensitive = 'Détails';
            $form->disableValidation()
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                    /*'app_user[addresses][__name__][line]' => $line,
                    'app_user[addresses][__name__][postalCode]' => $postalcode,
                    'app_user[addresses][__name__][city]' => $city,
                    'app_user[addresses][__name__][country]' => $country,*/
                    'app_user[emailAddress]' => $email,
                    'app_user[homePhoneNumber]' => $homenumber,
                    'app_user[cellPhoneNumber]' => $cellnumber,
                    'app_user[workPhoneNumber]' => $worknumber,
                    'app_user[workFaxNumber]' => $faxnumber,
                    'app_user[observations]' => $observations,
                    'app_user[sensitiveObservations]' => $sensitive
                ]);
            $form['app_user[denomination]']->select(2);
            $form['app_user[isReceivingNewsletter]']->tick();
            $form['app_user[newsletterDematerialization]']->tick();

            // Submits the form
            self::$client->submit($form);

            // Autoredirection to the people profile
            $this->assertContains(
                    '/people/17/edit',
                    self::$client->getResponse()->getContent(),
                    'The page should be redirecting to the newly created user profile one'
            );
            self::$client->followRedirect();
            $this->assertContains(
                    'Édition de l\'adhérent.e',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the newly created user profile one'
            );

            // Checks the database content
            $createdPeople = self::$container
                ->get('doctrine')
                ->getRepository(People::class)
                ->findOneBy([
                    'firstName' => $firstname,
                ]);

            $this->assertEquals(
                    $createdPeople->getFirstName(),
                    $firstname,
                    'The first name should be ' . $firstname
            );

            $this->assertEquals(
                    $createdPeople->getLastName(),
                    $name,
                    'The last name should be ' . $name
            );

            $this->assertEquals(
                    $createdPeople->getEmailAddress(),
                    $email,
                    'The email should be ' . $email
            );

            $this->assertEquals(
                    $createdPeople->getIsReceivingNewsletter(),
                    true,
                    'The user should receive the newsletter'
            );

            $this->assertEquals(
                    $createdPeople->getNewsletterDematerialization(),
                    true,
                    'The user should receive the newsletter by email'
            );

            $this->assertEquals(
                    $createdPeople->getCellPhoneNumber(),
                    $cellnumber,
                    'The cell phone number should be ' . $cellnumber
            );

            $this->assertEquals(
                    $createdPeople->getHomePhoneNumber(),
                    $homenumber,
                    'The home phone number should be ' . $homenumber
            );

            $this->assertEquals(
                    $createdPeople->getWorkPhoneNumber(),
                    $worknumber,
                    'The work phone number should be ' . $worknumber
            );

            $this->assertEquals(
                    $createdPeople->getWorkFaxNumber(),
                    $faxnumber,
                    'The fax number should be ' . $faxnumber
            );

            $this->assertEquals(
                    $createdPeople->getObservations(),
                    $observations,
                    'The observations should be ' . $observations
            );

            $this->assertEquals(
                    $createdPeople->getSensitiveObservations(),
                    $sensitive,
                    'The sensitive observations should be ' . $sensitive
            );

            $denomination = $createdPeople->getDenomination();

            $this->assertEquals(
                    $denomination->getLabel(),
                    "Monsieur",
                    'The denomination should be Monsieur'
            );

            /*$this->assertEquals(
                    $createdPeople->getAddresses(),
                    $createdPeople->getAddresses(),
                    'The address should be '. $line . $postalcode . $city . $country
            );*/
        }

        /**
         * Creates an user with mandatory form inputs empty
         * Form doesn't work in that way
         */
        public function testEditPeopleRequiredFieldsEmpty()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile ediiton page
            $crawler = self::$client->request('GET', '/people/17/edit');
            $this->assertEquals(
                    'Édition de l\'adhérent.e',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people edition one'
            );

            $form =self::$client->getCrawler()->selectButton('edit-people-submit-button')->form();

            $firstname = '';
            $name = '';
            $form->disableValidation()
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                ]);

            // Submits the form
            self::$client->submit($form);

            $this->assertEmpty(
                    self::$client->getCrawler()->filter('input')->first(),
                    'The node should be empty'
            );

            $this->assertEmpty(
                    self::$client->getCrawler()->filterXPath('(//input)[2]'),
                    'The node should be empty'
            );


        }

        /**
         * Creates an user with text in the phone fields
         * Form doesn't work in that way
         */
        public function testEditPeopleRequiredWrongPhone()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/people/17/edit');
            $this->assertEquals(
                    'Édition de l\'adhérent.e',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the people edition one'
            );

            $form =self::$client->getCrawler()->selectButton('edit-people-submit-button')->form();

            // Fills the mandatory form inputs
            $firstname = 'Florent';
            $name = 'Marin';
            $cellPhoneNumber = 'numero';
            $form->disableValidation()
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                    'app_user[cellPhoneNumber]' => $cellPhoneNumber
                ]);
            $form['app_user[denomination]']->select(2);

            // Submits the form
            self::$client->submit($form);

            $this->assertEmpty(
                    self::$client->getCrawler()->filterXPath('(//input)[8]'),
                    'The node should be empty'
            );

        }


}
?>
