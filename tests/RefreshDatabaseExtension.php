<?php

namespace App\Tests;

use PHPUnit\Runner\Extension\Extension as PhpunitExtension;

class RefreshDatabaseExtension extends Extension
{
    public static $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite()
    {
        try {
            /** @var \Codeception\Module\Cli $cli */
            $cli = $this->getModule('Cli');

            $this->writeln('Recreating the DB...');
            $cli->runShellCommand('bin/console doctrine:database:drop --if-exists --force');
            $cli->seeResultCodeIs(0);
            $cli->runShellCommand('bin/console doctrine:database:create');
            $cli->seeResultCodeIs(0);

            $this->writeln('Running Doctrine Migrations...');
            $cli->runShellCommand('bin/console doctrine:migrations:migrate --no-interaction');
            $cli->seeResultCodeIs(0);

            $this->writeln('Test database recreated');
        } catch (\Exception $e) {
            $this->writeln(
                sprintf(
                    'An error occurred whilst rebuilding the test database: %s',
                    $e->getMessage()
                )
            );
        }
    }
}
