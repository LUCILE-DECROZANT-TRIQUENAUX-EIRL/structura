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
class PeopleListManagerTest extends WebTestCase
{

    /**
     * Connects to the server with the gestiSensible user
     * Uses ChromeDriver
     */
    public function connectionChrome()
    {
        $mink = new Mink(array(
            'browser' => new Session(new ChromeDriver('http://localhost:9222', null, 'http://www.google.com'))
        ));
        // set the default session name
        $mink->setDefaultSessionName('browser');

        // Go to profile page
        $mink->getSession()->visit('http://localhost:8000/people/');
        $mink->getSession()->getPage()->fillField("_username", "gestiSensible");
        $mink->getSession()->getPage()->fillField("_username", "gestiSensible");
        $mink->getSession()->getPage()->fillField("_password", "a");
        $mink->getSession()->getPage()->fillField("_password", "a");
        // Connects
        $node = new NodeElement('//button[contains(.,"Connexion")]', $mink->getSession());
        $node->click();

        return $mink;
    }

    /**
     * Test that the list displays properly
     * Should be 10 rows for users + 1 row for header + 1 that i cant find (oops)
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
        $this->assertEquals(13, $crawler->filter('tr')->count());
        // Counts the number of "show profile" buttons
        $this->assertEquals(10, $crawler->filterXPath('//a[@data-original-title="Voir le profil"]')->count());
        // Counts the number of "edit profile" buttons
        $this->assertEquals(10, $crawler->filterXPath('//a[@data-original-title="Éditer le profil"]')->count());
        // Counts the number of "delete profile" buttons
        $this->assertEquals(10, $crawler->filterXPath('//a[@data-original-title="Supprimer le profil"]')->count());
        //Counts the number of add new people buttons
        $this->assertEquals(1, $crawler->filterXPath('//button[contains(@id,"createButton")]')->count());
        //Verifies that the breadcrumb is correct
        $this->assertEquals(1, $crawler->filterXPath('//li/a[contains(.,"Accueil")]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//li[contains(.,"Liste des adhérent·es")]')->count());
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

        $this->assertEquals(1, $crawler->filterXPath('//a[contains(.,"Précédent")]')->count());
        $this->assertEquals(1, $crawler->filterXPath('//a[contains(.,"Suivant")]')->count());
        // Go to the 2nd page
        $node = new NodeElement('//a[contains(.,"2")]', $client->getSession());
        $node->click();

        // Counts the number of rows on the new page
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertEquals(7, $crawler->filter('tr')->count());
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
        $this->assertContains('Bullion', $crawler->filterXPath('(//tr)[2]')->text());
        $node = new NodeElement('//th[contains(.,"Nom")]', $client->getSession());
        $node->click();

        // Then in descending
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertContains('Vérany', $crawler->filterXPath('(//tr)[2]')->text());

        // Test first name testSorting
        $node = new NodeElement('//th[contains(.,"Prénom")]', $client->getSession());
        $node->click();
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertContains('Agathe', $crawler->filterXPath('(//tr)[2]')->text());
        $node = new NodeElement('//th[contains(.,"Prénom")]', $client->getSession());
        $node->click();
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertContains('Tobie', $crawler->filterXPath('(//tr)[2]')->text());

    }

}

