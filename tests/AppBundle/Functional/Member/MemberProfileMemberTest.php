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

     public function connection()
     {
         $client = static::createClient();
         $container = static::$kernel->getContainer();
         $session = $container->get('session');

         // Get the admin user
         $person = self::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User')->findOneByUsername('adhe4');
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
      * Test that the user can't access the page
      */
     public function testError()
     {
         // Create a new client to browse the app
         $client = $this->connection();
         $crawler = $client->request('GET', '/member/31');
         $this->assertSame(404, $client->getResponse()->getStatusCode());
     }
}

?>
