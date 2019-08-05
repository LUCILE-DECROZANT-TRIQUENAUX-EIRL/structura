<?php
namespace Tests\App\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Element\NodeElement;
use DMore\ChromeDriver\ChromeDriver;

// I didn't test that the menu were there
// They're tested on the Home folder
// Here it would be the same function
// Or new functions would need to be written using Mink
class AdministrationListAdminTest extends WebTestCase
{

    /**
     * Connects to the server with the admin user //TODO: change to adminUniquement
     * Uses ChromeDriver
     */
    public function connectionChrome()
    {
        $mink = new Mink(array(
            'browser' => new Session(new ChromeDriver('http://localhost:9222', null, 'http://www.google.com'))
        ));
        // set the default session name
        $mink->setDefaultSessionName('browser');

        $mink->getSession()->visit('http://127.0.0.1:8000/');

        // !! Keep the same double fillField, otherwise it can lead to errors
        $mink->getSession()->getPage()->fillField("_username", "admin");
        sleep(1);
        $mink->getSession()->getPage()->fillField("_username", "admin");

        $mink->getSession()->getPage()->fillField("_password", "a");
        $mink->getSession()->getPage()->fillField("_password", "a");

        // Connects
        $node = new NodeElement('//button[contains(.,"Connexion")]', $mink->getSession());
        $node->click();
        sleep(1);

        // Go to Members page
        $node = new NodeElement('//a[contains(.,"Administration")]', $mink->getSession());
        $node->click();

        // Go to Members page
        $node = new NodeElement('//a[contains(.,"Liste des comptes")]', $mink->getSession());
        $node->click();

        return $mink;
    }

    /**
     * Test that the list displays properly
     * Should be 10 rows for users + 1 row for header
     * Also verifies that user associated buttons are there
     */
    public function testList()
    {
        $client = $this->connectionChrome();
        $this->assertSame(Response::HTTP_OK, $client->getSession()->getStatusCode());
        // Gets the html content of the page
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        // Counts the number of rows
        $this->assertEquals(12, $crawler->filter('tr')->count());
        // Counts the number of "show profile" buttons
        $this->assertEquals(10, $crawler->filterXPath('//a[@data-original-title="Voir le profil"]')->count());
        // Counts the number of "edit profile" buttons
        $this->assertEquals(10, $crawler->filterXPath('//a[@data-original-title="Éditer le profil"]')->count());
        // Counts the number of "delete profile" buttons
        $this->assertEquals(10, $crawler->filterXPath('//a[@data-original-title="Supprimer le profil"]')->count());
    }

    /**
     * Test the navigation between pages
     */
    public function testNavigation()
    {
        $client = $this->connectionChrome();
        $this->assertSame(Response::HTTP_OK, $client->getSession()->getStatusCode());
        // Gets the html content of the page
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());

        $this->assertEquals(1, $crawler->filterXPath('//a[contains(.,"Previous")]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//a[contains(.,"Next")]')->count());

        // Go to the 2nd page
        $node = new NodeElement('(//a[contains(.,"2")])[2]', $client->getSession());
        $node->click();
        // Counts the number of rows on the new page
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertEquals(6, $crawler->filter('tr')->count());
    }

    /**
     * Tests the sorting
     */
    public function testSorting()
    {
        $client = $this->connectionChrome();
        $this->assertSame(Response::HTTP_OK, $client->getSession()->getStatusCode());
        // Gets the html content of the page
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());

        // Test name sorting
        // At first the name are listed in ascending order
        $this->assertContains('1', $crawler->filterXPath('(//tr)[2]')->text());
        $node = new NodeElement('//th[contains(.,"Identifiant")]', $client->getSession());
        $node->click();

        // Then in descending
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertContains('14', $crawler->filterXPath('(//tr)[2]')->text());

        // Test first name testSorting
        $node = new NodeElement('//th[contains(.,"Nom d\'utilisateurice")]', $client->getSession());
        $node->click();
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertContains('adhe1', $crawler->filterXPath('(//tr)[2]')->text());
        $node = new NodeElement('//th[contains(.,"Nom d\'utilisateurice")]', $client->getSession());
        $node->click();
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertContains('test2', $crawler->filterXPath('(//tr)[2]')->text());

    }

}

?>
