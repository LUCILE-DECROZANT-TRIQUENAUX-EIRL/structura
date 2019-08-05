<?php
namespace Tests\App\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

// TODO : Doesn't work, see issue 51
class HomeAdminTest extends WebTestCase
{

    /**
     * Connect to the website while being logged in
     * Logs in with (username : adminUniquement, password : a)
     */
    public function connection()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');

        // Get the admin user
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('adminUniquement');
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
     * Test if the Administration menu is here
     */
    public function testMenuAdmin()
    {
        // Create a new client to browse the app
        $client = $this->connection();
        $crawler = $client->request('GET', '/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains(
                'Administration',
                $client->getResponse()->getContent()
        );
    }

    /**
     * Test if the Members menu isn't here
     */
    public function testMenuMembers()
    {
        // Create a new client to browse the app
        $client = $this->connection();
        $crawler = $client->request('GET', '/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertNotContains(
                    'Adhérent·es',
                    $client->getResponse()->getContent()
        );
    }

    /**
     * Test profile menu
     */
    public function testMenuProfile()
    {
        // Create a new client to browse the app
        $client = $this->connection();
        $crawler = $client->request('GET', '/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertContains(
                    'adminUniquement',
                    $client->getResponse()->getContent()
        );
    }

}

?>
