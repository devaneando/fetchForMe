<?php

// Check https://symfony.com/doc/3.4/testing/bootstrap.html

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    // executes the "php bin/console cache:clear" command
    passthru(
        sprintf(
            'php %s/../bin/console %s --env=%s %s',
            __DIR__,
            'cache:clear',
            $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'],
            '--quiet --no-optional-warmers --no-warmup'
        )
    );
}

if (isset($_ENV['BOOTSTRAP_SCHEMA_DROP_ENV'])) {
    // executes the "php bin/console doctrine:schema:drop --force" command
    passthru(
        sprintf(
            'php %s/../bin/console %s --env=%s %s',
            __DIR__,
            'doctrine:schema:drop',
            $_ENV['BOOTSTRAP_SCHEMA_DROP_ENV'],
            '--quiet --force'
        )
    );
}

if (isset($_ENV['BOOTSTRAP_SCHEMA_CREATE_ENV'])) {
    // executes the "bin/console doctrine:schema:create --env=test" command
    passthru(
        sprintf(
            'php %s/../bin/console %s --env=%s %s',
            __DIR__,
            'doctrine:schema:create',
            $_ENV['BOOTSTRAP_SCHEMA_CREATE_ENV'],
            '--quiet '
        )
    );
}

if (isset($_ENV['BOOTSTRAP_MIGRATE_ENV'])) {
    // executes the "bin/console doctrine:migrations:migrate --no-interaction" command
    passthru(
        sprintf(
            'php %s/../bin/console %s --env=%s %s',
            __DIR__,
            'doctrine:migrations:migrate',
            $_ENV['BOOTSTRAP_MIGRATE_ENV'],
            '--no-interaction --quiet'
        )
    );
}

if (isset($_ENV['BOOTSTRAP_FIXTURES_LOAD_ENV'])) {
    // executes the "bin/console doctrine:fixtures:load --env=test --no-interaction" command
    passthru(
        sprintf(
            'php %s/../bin/console %s --env=%s %s',
            __DIR__,
            'doctrine:fixtures:load',
            $_ENV['BOOTSTRAP_FIXTURES_LOAD_ENV'],
            '--no-debug --quiet --no-interaction'
        )
    );
}

require __DIR__.'/../vendor/autoload.php';
