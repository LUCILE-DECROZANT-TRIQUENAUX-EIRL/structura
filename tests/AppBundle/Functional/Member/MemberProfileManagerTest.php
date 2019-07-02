<?php
namespace Tests\AppBundle\Functional;
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
class MemberProfileManagerTest extends WebTestCase
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
        $mink->getSession()->visit('http://127.0.0.1:8000/');
        // !! Keep the same double fillField, otherwise it can lead to errors
        $mink->getSession()->getPage()->fillField("_username", "gestiSensible");
        $mink->getSession()->getPage()->fillField("_username", "gestiSensible");
        $mink->getSession()->getPage()->fillField("_password", "a");
        $mink->getSession()->getPage()->fillField("_password", "a");
        // Connects
        $node = new NodeElement('//button[contains(.,"Connexion")]', $mink->getSession());
        $node->click();
        // Go to Members page
        $node = new NodeElement('//a[contains(.,"Adhérent·es")]', $mink->getSession());
        $node->click();
        // Go to profile page
        $node = new NodeElement('(//a[@data-original-title="Voir le profil"])[1]', $mink->getSession());
        $node->click();
        return $mink;
    }
    /**
     * Test that the selection acts properly
     */
    public function testSelectProfile()
    {
        $client = $this->connectionChrome();
        $this->assertSame(Response::HTTP_OK, $client->getSession()->getStatusCode());
        // Gets the html content of the page
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        // Verifies the selected user
        $this->assertContains('Docteur Ladislas Bullion', $crawler->filterXPath('//h1')->text());
        // Verifies the infos are loaded
        $this->assertContains('Informations de contact', $crawler->filterXPath('//h3')->text());
        $this->assertContains('Informations personnelles', $crawler->filterXPath('(//h3)[2]')->text());
    }
    /**
     * Test that the menu is displayed properly
     */
    public function testMenu()
    {
        $client = $this->connectionChrome();
        $this->assertSame(Response::HTTP_OK, $client->getSession()->getStatusCode());
        // Gets the html content of the page
        $page = $client->getSession()->getPage();
        $crawler = new Crawler($page->getContent());
        $this->assertContains('Profil', $crawler->filterXPath('//ul/a')->text());
        $this->assertContains('Adhésions', $crawler->filterXPath('(//ul/a)[2]')->text());
        $this->assertContains('Dons', $crawler->filterXPath('(//ul/a)[3]')->text());
        $this->assertContains('Édition', $crawler->filterXPath('(//ul/a)[4]')->text());
    }
}
?>
