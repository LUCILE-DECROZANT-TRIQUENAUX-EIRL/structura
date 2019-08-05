<?php

function bootstrap()
{
    // Clearing cache if the BOOTSTRAP_CLEAR_CACHE_ENV var is set in the phpunit.xml.dist
    if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
        passthru(sprintf(
            'php "%s/../bin/console" cache:clear --env='.$_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'].' --no-warmup',
            __DIR__
        ));
    }

    passthru(sprintf(
        'php "%s/../bin/console" doctrine:database:drop --env=test --no-interaction --force --if-exists',
        __DIR__
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:database:create --env=test --no-interaction',
        __DIR__
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:migration:migrate --env=test --no-interaction',
        __DIR__
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:fixtures:load --env=test --append --no-interaction',
        __DIR__
    ));
}

bootstrap();

require_once __DIR__.'/../vendor/autoload.php';