<?php
namespace Tests\App\DataFixtures;

// use Doctrine\ORM\EntityManager;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Symfony\Bundle\FrameworkBundle\Console\Application;
// use Symfony\Component\Console\Input\StringInput;
// use Symfony\Bundle\FrameworkBundle\Client;
// use Symfony\Component\DependencyInjection\ContainerInterface;

// class DataFixturesTestCase extends WebTestCase
// {
//     /** @var  Application $application */
//     protected static $application;

//     /** @var  Client $client */
//     protected $client;

//     /** @var  ContainerInterface $container */
//     protected $container;

//     /** @var  EntityManager $entityManager */
//     protected $entityManager;

//     /**
//      * {@inheritDoc}
//      */
//     public function setUp()
//     {
//         echo 'setUp';

//         self::runCommand('doctrine:database:drop --force');
//         self::runCommand('doctrine:database:create');
//         self::runCommand('doctrine:migration:migrate --no-interaction');
//         self::runCommand('doctrine:fixtures:load --append --no-interaction');

//         $this->client = static::createClient();
//         $this->container = $this->client->getContainer();
//         $this->entityManager = $this->container->get('doctrine.orm.entity_manager');

//         parent::setUp();
//     }

//     protected static function runCommand($command)
//     {
//         $command = sprintf('%s --quiet', $command);

//         return self::getApplication()->run(new StringInput($command));
//     }

//     protected static function getApplication()
//     {
//         if (null === self::$application) {
//             $client = static::createClient();

//             self::$application = new Application($client->getKernel());
//             self::$application->setAutoExit(false);
//         }

//         return self::$application;
//     }

//     /**
//      * {@inheritDoc}
//      */
//     protected function tearDown()
//     {
//         echo 'tearDown';

//         self::runCommand('doctrine:database:drop --force');

//         parent::tearDown();

//         $this->entityManager->close();
//         $this->entityManager = null; // avoid memory leaks
//     }
// }