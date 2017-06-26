<?php

namespace Bezhanov\Silex\AliceDataFixtures\Tests;

use Bezhanov\Silex\AliceDataFixtures\FixturesLoader;
use Bezhanov\Silex\AliceDataFixtures\FixturesServiceProvider;
use Bezhanov\Silex\AliceDataFixtures\LoadFixturesCommand;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Prophecy\Argument;
use Symfony\Component\Console\Application as Console;

class FixturesServiceProviderTest extends TestCase
{
    public function testRegisterWithoutOrmInContainer()
    {
        $this->expectException(\RuntimeException::class);
        $console = new Console();
        $fixturesServiceProvider = new FixturesServiceProvider($console);
        $fixturesServiceProvider->register(new Container());
    }

    public function testRegister()
    {
        $container = new Container();
        $container['orm.em'] = $this->createEntityManagerMock();
        $console = new Console();
        $fixturesServiceProvider = new FixturesServiceProvider($console);
        $fixturesServiceProvider->register($container);
        $command = $console->find('fixtures:load');
        $this->assertInstanceOf(FixturesLoader::class, $container['fixtures.loader']);
        $this->assertInstanceOf(ORMPurger::class, $container['fixtures.purger']);
        $this->assertInstanceOf(ORMExecutor::class, $container['fixtures.executor']);
        $this->assertInstanceOf(LoadFixturesCommand::class, $command);
    }

    /**
     * @return EntityManagerInterface
     */
    private function createEntityManagerMock()
    {
        $eventManagerMock = $this->createEventManagerMock();
        $mock = $this->prophesize(EntityManagerInterface::class);
        $mock->getEventManager()->shouldBeCalledTimes(1)->willReturn($eventManagerMock);
        return $mock->reveal();
    }

    /**
     * @return EventManager
     */
    private function createEventManagerMock()
    {
        $mock = $this->prophesize(EventManager::class);
        $mock->addEventSubscriber(Argument::any())->shouldBeCalledTimes(1);
        return $mock->reveal();
    }
}
