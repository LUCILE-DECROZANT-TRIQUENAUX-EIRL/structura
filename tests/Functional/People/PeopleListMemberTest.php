<?php
namespace Tests\App\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

// TODO : Doesn't work, see issue 51
class PeopleListPeopleTest extends WebTestCase
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
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('adhe4');
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
        $crawler = $client->request('GET', '/people/');
        $this->assertSame(403, $client->getResponse()->getStatusCode());
    }

}

