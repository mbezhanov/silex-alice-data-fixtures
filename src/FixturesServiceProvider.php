<?php

namespace Bezhanov\Silex\AliceDataFixtures;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Application as Console;

class FixturesServiceProvider implements ServiceProviderInterface
{
    private $console;

    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    public function register(Container $container)
    {
        if (!isset($container['orm.em'])) {
            throw new \RuntimeException('Unable to retrieve Entity Manager from Service Container.');
        }

        $container['fixtures.loader'] = function() {
            return new FixturesLoader();
        };

        $container['fixtures.purger'] = function() {
            return new ORMPurger();
        };

        $container['fixtures.executor'] = function(Container $container) {
            return new ORMExecutor($container['orm.em'], $container['fixtures.purger']);
        };

        $this->console->add(new LoadFixturesCommand($container));
    }
}
