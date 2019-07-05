<?php
namespace Tests\AppBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;

// TODO : Doesn't work, see issue 51
class MemberListMemberTest extends WebTestCase
{
    /**
     * Connect to the website while being logged in
     * Logs in with (username : adhe4, password : a)
     */
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
        $crawler = $client->request('GET', '/member/');
        $this->assertSame(403, $client->getResponse()->getStatusCode());
    }

}

?>
