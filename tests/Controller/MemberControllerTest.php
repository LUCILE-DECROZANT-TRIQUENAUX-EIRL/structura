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

class MemberControllerTest extends WebTestCase
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

//=========== Create a member

    //  -------------------------------------------------
    //   Test the access of the member create profile page
    //  -------------------------------------------------
        /**
         * Tests if a person with the gestionnaire role can access the page
         */
        public function testGestionnaireAccessCreateMemberPage()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Go to the profile creation page
            self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member creation one'
            );

            // Connects the gestionnaire sensible
            $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

            // Go to the profile creation page
            self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member creation one'
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
        public function testAdminAccessCreateMemberPage()
        {

            // Connects the admin only
            $this->connection(self::ADMIN_ONLY_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );

            // Connects the admin
            // Has also gestionnaire role so can access the page
            $this->connection(self::ADMIN_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member creation one'
            );
        }

        /**
         * Tests if a person with the adherente role can access the page
         */
        public function testAdherenteAccessCreateMemberPage()
        {
            // Connects the adherent.e
            $this->connection(self::ADHERENTE_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );
        }

        /**
         * Tests if a person with the Sympathisant role can access the page
         */
        public function testSympathisanteAccessCreateMemberPage()
        {
            // Connect the adherent.e
            $this->connection(self::SYMPATHISANTE_USERNAME);

            // Go to the profile creation page
            self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );
        }

        /**
         * Tests if a person with the mécène role can access the page
         */
        public function testMeceneAccessCreateMemberPage()
        {
            // Connects the adherent.e
            $this->connection(self::MECENE_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    403,
                    self::$client->getResponse()->getStatusCode(),
                    'The user shouldn\'t be allowed to access the page'
            );
        }

        /**
         * Tests if a person with the inscrit role can access the page
         */
        public function testInscriteAccessCreateMemberPage()
        {
            // Connects the adherent.e
            $this->connection(self::INSCRITE_USERNAME);

            // Goes to the profile creation page
            self::$client->request('GET', '/member/new');
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
            self::$client->request('GET', '/member/new');
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
            self::$client->request('GET', '/member/new');
            $this->assertContains(
                'Détails médicaux',
                self::$client->getCrawler()->filter('label')->last()->text(),
                'The page should contain sensitive observations'
            );

        }


    //  -----------------------------
    //   Test the creation of a member
    //  -----------------------------

        /**
         * Creates an user with mandatory form inputs
         */
        public function testCreateMemberRequiredFields()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-member-submit-button')->form();
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

            // Autoredirection to the member profile
            $this->assertContains(
                    'Redirecting to <a href="/member/18">/member/18</a>.',
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
            $createdMember = self::$container
                ->get('doctrine')
                ->getRepository(People::class)
                ->findOneBy([
                    'firstName' => $firstname,
                ]);

            $this->assertEquals(
                    $createdMember->getFirstName(),
                    $firstname,
                    'The first name should be ' . $firstname
            );

            $this->assertEquals(
                    $createdMember->getLastName(),
                    $name,
                    'The last name should be ' . $name
            );

            $denomination =$createdMember->getDenomination();

            $this->assertEquals(
                    $denomination->getLabel(),
                    "Monsieur",
                    'The denomination should be Monsieur '
            );
        }

        /**
         * Creates an user with all form inputs
         */
        public function testCreateMemberAllFields()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-member-submit-button')->form();
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

            // Autoredirection to the member profile
            $this->assertContains(
                    'Redirecting to <a href="/member/18">/member/18</a>.',
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
            $createdMember = self::$container
                ->get('doctrine')
                ->getRepository(People::class)
                ->findOneBy([
                    'firstName' => $firstname,
                ]);

            $this->assertEquals(
                    $createdMember->getFirstName(),
                    $firstname,
                    'The first name should be ' . $firstname
            );

            $this->assertEquals(
                    $createdMember->getLastName(),
                    $name,
                    'The last name should be ' . $name
            );

            $this->assertEquals(
                    $createdMember->getEmailAddress(),
                    $email,
                    'The email should be ' . $email
            );

            $this->assertEquals(
                    $createdMember->getIsReceivingNewsletter(),
                    true,
                    'The user should receive the newsletter'
            );

            $this->assertEquals(
                    $createdMember->getNewsletterDematerialization(),
                    true,
                    'The user should receive the newsletter by email'
            );

            $this->assertEquals(
                    $createdMember->getCellPhoneNumber(),
                    $cellnumber,
                    'The cell phone number should be ' . $cellnumber
            );

            $this->assertEquals(
                    $createdMember->getHomePhoneNumber(),
                    $homenumber,
                    'The home phone number should be ' . $homenumber
            );

            $this->assertEquals(
                    $createdMember->getWorkPhoneNumber(),
                    $worknumber,
                    'The work phone number should be ' . $worknumber
            );

            $this->assertEquals(
                    $createdMember->getWorkFaxNumber(),
                    $faxnumber,
                    'The fax number should be ' . $faxnumber
            );

            $this->assertEquals(
                    $createdMember->getObservations(),
                    $observations,
                    'The observations should be ' . $observations
            );

            $this->assertEquals(
                    $createdMember->getSensitiveObservations(),
                    $sensitive,
                    'The sensitive observations should be ' . $sensitive
            );

            $denomination = $createdMember->getDenomination();

            $this->assertEquals(
                    $denomination->getLabel(),
                    "Monsieur",
                    'The denomination should be Monsieur'
            );

            $this->assertEquals(
                    $createdMember->getAddresses(),
                    $createdMember->getAddresses(),
                    'The address should be '. $line . $postalcode . $city . $country
            );
        }

        /**
         * Creates an user with mandatory form inputs empty
         * Form doesn't work in that way
         */
        public function testCreateMemberRequiredFieldsEmpty()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-member-submit-button')->form();

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
        public function testCreateMemberRequiredWrongPhone()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile creation page
            $crawler = self::$client->request('GET', '/member/new');
            $this->assertEquals(
                    'Enregistrer une nouvelle personne dans l\'annuaire',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member creation one'
            );


            $form =self::$client->getCrawler()->selectButton('create-member-submit-button')->form();

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

//========= Edit a member

//  -------------------------------------------------
//   Tests the access of the member edit profile page
//  -------------------------------------------------
    /**
     * Tests if a person with the gestionnaire role can access the page
     */
    public function testGestionnaireAccessEditMemberPage()
    {
        // Connects the gestionnaire
        $this->connection(self::GESTIONNAIRE_USERNAME);

        // Go to the profile edition page
        self::$client->request('GET', '/member/17/edit');
        $this->assertEquals(
                'Édition de l\'adhérent.e',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the member edition one'
        );

        // Connects the gestionnaire sensible
        $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);

        // Go to the profile edition page
        self::$client->request('GET', '/member/17/edit');
        $this->assertEquals(
                'Édition de l\'adhérent.e',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the member edition one'
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
                'The breadcrumb should link to the member profile page'
        );
        $this->assertContains(
                'Éditer le profil de Jeanne Carton' ,
                self::$client->getCrawler()->filterXPath('(//li)[6]')->text(),
                'The breadcrumb should be on the edit member page'
        );


    }

    /**
     * Tests if a person with the admin role can access the page
     */
    public function testAdminAccessEditMemberPage()
    {

        // Connects the admin only
        $this->connection(self::ADMIN_ONLY_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/member/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );

        // Connects the admin
        // Has also gestionnaire role so can access the page
        $this->connection(self::ADMIN_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/member/17/edit');
        $this->assertEquals(
                'Édition de l\'adhérent.e',
                self::$client->getCrawler()->filter('h1')->first()->text(),
                'The page should be the member edition one'
        );
    }

    /**
     * Tests if a person with the adherente role can access the page
     */
    public function testAdherenteAccessEditMemberPage()
    {
        // Connects the adherent.e
        $this->connection(self::ADHERENTE_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/member/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * Tests if a person with the Sympathisant role can access the page
     */
    public function testSympathisanteAccessEditMemberPage()
    {
        // Connect the adherent.e
        $this->connection(self::SYMPATHISANTE_USERNAME);

        // Go to the profile creation page
        self::$client->request('GET', '/member/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * Tests if a person with the mécène role can access the page
     */
    public function testMeceneAccessEditMemberPage()
    {
        // Connects the adherent.e
        $this->connection(self::MECENE_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/member/17/edit');
        $this->assertEquals(
                403,
                self::$client->getResponse()->getStatusCode(),
                'The user shouldn\'t be allowed to access the page'
        );
    }

    /**
     * Tests if a person with the inscrit role can access the page
     */
    public function testInscriteAccessEditMemberPage()
    {
        // Connects the adherent.e
        $this->connection(self::INSCRITE_USERNAME);

        // Goes to the profile creation page
        self::$client->request('GET', '/member/17/edit');
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
        self::$client->request('GET', '/member/17/edit');
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
        self::$client->request('GET', '/member/17/edit');
        $this->assertContains(
            'Détails médicaux',
            self::$client->getCrawler()->filter('label')->last()->text(),
            'The page should contain sensitive observations'
        );

    }

    //  -----------------------------
    //   Test the edition of a member
    //  -----------------------------

        /**
         * Creates an user with mandatory form inputs
         * @group test
         */
        public function testEditMemberRequiredFields()
        {
            // Connects the gestionnaire
            $this->connection(self::GESTIONNAIRE_USERNAME);

            // Goes to the profile ediiton page
            $crawler = self::$client->request('GET', '/member/17/edit');
            $this->assertEquals(
                    'Édition de l\'adhérent.e',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the member edition one'
            );


            $form =self::$client->getCrawler()->selectButton('edit-member-submit-button')->form();

            // Fills the mandatory form inputs
            /*$firstname = 'Florent';
            $name = 'Marin';
            $form
                ->setValues([
                    'app_user[firstname]' => $firstname,
                    'app_user[lastname]' => $name,
                ]);*/
            //$form['app_user[denomination]']->select(2);

            // Submits the form
            self::$client->submit($form);
    //
    //         // Autoredirection to the member profile
    //         /*$this->assertContains(
    //                 'Redirecting to <a href="/member/17/edit">/member/17/edit</a>.',
    //                 self::$client->getResponse()->getContent(),
    //                 'The page should be redirecting to the newly created user profile one'
    //         );*/
            //self::$client->followRedirect();
            $this->assertContains(
                    'Monsieur Florent Marin',
                    self::$client->getCrawler()->filter('h1')->first()->text(),
                    'The page should be the newly created user profile one'
            );
    //
    //         // Checks the database content
    //         $createdMember = self::$container
    //             ->get('doctrine')
    //             ->getRepository(People::class)
    //             ->findOneBy([
    //                 'firstName' => $firstname,
    //             ]);
    //
    //         $this->assertEquals(
    //                 $createdMember->getFirstName(),
    //                 $firstname,
    //                 'The first name should be ' . $firstname
    //         );
    //
    //         $this->assertEquals(
    //                 $createdMember->getLastName(),
    //                 $name,
    //                 'The last name should be ' . $name
    //         );
    //
    //         $denomination =$createdMember->getDenomination();
    //
    //         $this->assertEquals(
    //                 $denomination->getLabel(),
    //                 "Monsieur",
    //                 'The denomination should be Monsieur '
    //         );
       }

        /**
         * Creates an user with all form inputs
         */
        // public function testCreateMemberAllFields()
        // {
        //     // Connects the gestionnaire
        //     $this->connection(self::GESTIONNAIRE_SENSIBLE_USERNAME);
        //
        //     // Goes to the profile creation page
        //     $crawler = self::$client->request('GET', '/member/new');
        //     $this->assertEquals(
        //             'Enregistrer une nouvelle personne dans l\'annuaire',
        //             self::$client->getCrawler()->filter('h1')->first()->text(),
        //             'The page should be the member creation one'
        //     );
        //
        //
        //     $form =self::$client->getCrawler()->selectButton('create-member-submit-button')->form();
        //     // Fills the mandatory form inputs
        //     $firstname = 'Florent';
        //     $name = 'Marin';
        //     $line = '30 rue Carnot';
        //     $postalcode = '42510';
        //     $city =  'Balbigny';
        //     $country = 'France';
        //     $email = 'marin@mail.fr';
        //     $homenumber = '0134567890';
        //     $cellnumber = '0134567890';
        //     $worknumber = '0134567890';
        //     $faxnumber = '0134567890';
        //     $observations = 'Observations';
        //     $sensitive = 'Détails';
        //     $form->disableValidation()
        //         ->setValues([
        //             'app_user[firstname]' => $firstname,
        //             'app_user[lastname]' => $name,
        //             'app_user[addresses][__name__][line]' => $line,
        //             'app_user[addresses][__name__][postalCode]' => $postalcode,
        //             'app_user[addresses][__name__][city]' => $city,
        //             'app_user[addresses][__name__][country]' => $country,
        //             'app_user[emailAddress]' => $email,
        //             'app_user[homePhoneNumber]' => $homenumber,
        //             'app_user[cellPhoneNumber]' => $cellnumber,
        //             'app_user[workPhoneNumber]' => $worknumber,
        //             'app_user[workFaxNumber]' => $faxnumber,
        //             'app_user[observations]' => $observations,
        //             'app_user[sensitiveObservations]' => $sensitive
        //         ]);
        //     $form['app_user[denomination]']->select(2);
        //     $form['app_user[isReceivingNewsletter]']->tick();
        //     $form['app_user[newsletterDematerialization]']->tick();
        //
        //     // Submits the form
        //     self::$client->submit($form);
        //
        //     // Autoredirection to the member profile
        //     $this->assertContains(
        //             'Redirecting to <a href="/member/18">/member/18</a>.',
        //             self::$client->getResponse()->getContent(),
        //             'The page should be redirecting to the newly created user profile one'
        //     );
        //     self::$client->followRedirect();
        //     $this->assertContains(
        //             'Monsieur Florent Marin',
        //             self::$client->getCrawler()->filter('h1')->first()->text(),
        //             'The page should be the newly created user profile one'
        //     );
        //
        //     // Checks the database content
        //     $createdMember = self::$container
        //         ->get('doctrine')
        //         ->getRepository(People::class)
        //         ->findOneBy([
        //             'firstName' => $firstname,
        //         ]);
        //
        //     $this->assertEquals(
        //             $createdMember->getFirstName(),
        //             $firstname,
        //             'The first name should be ' . $firstname
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getLastName(),
        //             $name,
        //             'The last name should be ' . $name
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getEmailAddress(),
        //             $email,
        //             'The email should be ' . $email
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getIsReceivingNewsletter(),
        //             true,
        //             'The user should receive the newsletter'
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getNewsletterDematerialization(),
        //             true,
        //             'The user should receive the newsletter by email'
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getCellPhoneNumber(),
        //             $cellnumber,
        //             'The cell phone number should be ' . $cellnumber
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getHomePhoneNumber(),
        //             $homenumber,
        //             'The home phone number should be ' . $homenumber
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getWorkPhoneNumber(),
        //             $worknumber,
        //             'The work phone number should be ' . $worknumber
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getWorkFaxNumber(),
        //             $faxnumber,
        //             'The fax number should be ' . $faxnumber
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getObservations(),
        //             $observations,
        //             'The observations should be ' . $observations
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getSensitiveObservations(),
        //             $sensitive,
        //             'The sensitive observations should be ' . $sensitive
        //     );
        //
        //     $denomination = $createdMember->getDenomination();
        //
        //     $this->assertEquals(
        //             $denomination->getLabel(),
        //             "Monsieur",
        //             'The denomination should be Monsieur'
        //     );
        //
        //     $this->assertEquals(
        //             $createdMember->getAddresses(),
        //             $createdMember->getAddresses(),
        //             'The address should be '. $line . $postalcode . $city . $country
        //     );
        // }
        //
        // /**
        //  * Creates an user with mandatory form inputs empty
        //  * Form doesn't work in that way
        //  */
        // public function testCreateMemberRequiredFieldsEmpty()
        // {
        //     // Connects the gestionnaire
        //     $this->connection(self::GESTIONNAIRE_USERNAME);
        //
        //     // Goes to the profile creation page
        //     $crawler = self::$client->request('GET', '/member/new');
        //     $this->assertEquals(
        //             'Enregistrer une nouvelle personne dans l\'annuaire',
        //             self::$client->getCrawler()->filter('h1')->first()->text(),
        //             'The page should be the member creation one'
        //     );
        //
        //
        //     $form =self::$client->getCrawler()->selectButton('create-member-submit-button')->form();
        //
        //     // Submits the form
        //     self::$client->submit($form);
        //
        //     $this->assertContains(
        //             'Symfony Exception',
        //             self::$client->getCrawler()->filter('h1')->first()->text(),
        //             'The page should have an Exception'
        //     );
        //
        // }
        //
        // /**
        //  * Creates an user with text in the phone fields
        //  * Form doesn't work in that way
        //  */
        // public function testCreateMemberRequiredWrongPhone()
        // {
        //     // Connects the gestionnaire
        //     $this->connection(self::GESTIONNAIRE_USERNAME);
        //
        //     // Goes to the profile creation page
        //     $crawler = self::$client->request('GET', '/member/new');
        //     $this->assertEquals(
        //             'Enregistrer une nouvelle personne dans l\'annuaire',
        //             self::$client->getCrawler()->filter('h1')->first()->text(),
        //             'The page should be the member creation one'
        //     );
        //
        //
        //     $form =self::$client->getCrawler()->selectButton('create-member-submit-button')->form();
        //
        //     // Fills the mandatory form inputs
        //     $firstname = 'Florent';
        //     $name = 'Marin';
        //     $cellPhoneNumber = 'numero';
        //     $form->disableValidation()
        //         ->setValues([
        //             'app_user[firstname]' => $firstname,
        //             'app_user[lastname]' => $name,
        //             'app_user[cellPhoneNumber]' => $cellPhoneNumber
        //         ]);
        //     $form['app_user[denomination]']->select(2);
        //
        //     // Submits the form
        //     self::$client->submit($form);
        //
        //     $this->assertContains(
        //             'Symfony Exception',
        //             self::$client->getCrawler()->filter('h1')->first()->text(),
        //             'The page should have an Exception'
        //     );
        //
        // }


}
?>
