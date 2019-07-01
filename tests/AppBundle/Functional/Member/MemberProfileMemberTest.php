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
class MemberProfileMemberest extends WebTestCase
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
        $mink->getSession()->getPage()->fillField("_username", "adhe4");
        $mink->getSession()->getPage()->fillField("_username", "adhe4");

        $mink->getSession()->getPage()->fillField("_password", "a");
        $mink->getSession()->getPage()->fillField("_password", "a");

        // Connects
        $node = new NodeElement('//button[contains(.,"Connexion")]', $mink->getSession());
        $node->click();

        // Go to Members page
        $node = new NodeElement('//a[contains(.,"Adhérent·es")]', $mink->getSession());
        $node->click();

        // Go to Members page
        $node = new NodeElement('(//a[@data-original-title="Voir le profil"])[1]', $mink->getSession());
        $node->click();

        return $mink;
    }

    /**
     * Test that the user can't access the page
     */
    public function testSelectProfile()
    {
        $client = $this->connectionChrome();
        $this->assertSame(403, $client->getSession()->getStatusCode());
    }
}

?>
