<?php

require_once __DIR__.'/../vendor/autoload.php';


function bootstrap()
{
    // Booting the kernel in test env
    $kernel = new \AppKernel('test', true);
    $kernel->boot();

    // Creating Symfony application
    $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    $application->setAutoExit(false);

    // Adding commands to execute
    $application->add(new \Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand());
    $application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());

    // Drop potential existing DB
    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => '1',
        '--force' => '1',
    ]));

    // Create the DB
    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:database:create',
    ]));

    // Applying migration
    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:migration:migrate',
        '--no-interaction' => '1',
    ]));

    // Loading the data fixtures
    $application->run(new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '--append' => '1',
        '--no-interaction' => '1',
    ]));

    // Clearing cache if the BOOTSTRAP_CLEAR_CACHE_ENV var is set in the phpunit.xml.dist
    if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
        $application->run(new \Symfony\Component\Console\Input\ArrayInput([
            'command' => 'cache:clear',
            '--env' => $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'],
            '--no-warmup' => '1',
        ]));
    }

    // Shutting down the kernel
    $kernel->shutdown();
}

bootstrap();