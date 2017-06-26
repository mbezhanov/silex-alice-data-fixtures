<?php

namespace Bezhanov\Silex\AliceDataFixtures\Tests;

use Bezhanov\Silex\AliceDataFixtures\FixturesServiceProvider;
use Bezhanov\Silex\AliceDataFixtures\LoadFixturesCommand;
use Bezhanov\Silex\AliceDataFixtures\Tests\Entity\Bar;
use Bezhanov\Silex\AliceDataFixtures\Tests\Entity\Foo;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\Output;

class LoadFixturesCommandTest extends TestCase
{
    public function testLoadFixturesFromFile()
    {
        $input = $this->createInputFromString('--fixtures="' . __DIR__ . '/fixtures.yml"');
        $output = new TestOutput();
        $application = $this->createTestApplication();
        $command = new LoadFixturesCommand($application);
        $command->run($input, $output);
        $this->assertFixturesHaveBeenLoaded($application);
    }

    public function testLoadFixturesFromDirectory()
    {
        $input = $this->createInputFromString('--fixtures="' . __DIR__ . '"');
        $output = new TestOutput();
        $application = $this->createTestApplication();
        $command = new LoadFixturesCommand($application);
        $command->run($input, $output);
        $this->assertFixturesHaveBeenLoaded($application);
    }

    public function testLoadFixturesFromMissingFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $input = $this->createInputFromString('');
        $output = new TestOutput();
        $application = $this->createTestApplication();
        $command = new LoadFixturesCommand($application);
        $command->run($input, $output);
    }

    public function testAppendFixtures()
    {
        $input = $this->createInputFromString('--fixtures="' . __DIR__ . '/fixtures.yml" --append');
        $output = new TestOutput();
        $application = $this->createTestApplication();
        $command = new LoadFixturesCommand($application);
        $command->run($input, $output);
        $this->assertFixturesHaveBeenLoaded($application, true);
    }

    public function testLoadFixturesWithPurgeWithTruncate()
    {
        $input = $this->createInputFromString('--fixtures="' . __DIR__ . '/fixtures.yml" --purge-with-truncate');
        $output = new TestOutput();
        $application = $this->createTestApplication();
        $command = new LoadFixturesCommand($application);
        $command->run($input, $output);
        $this->assertFixturesHaveBeenLoaded($application);
    }

    private function createInputFromString(string $string)
    {
        $input = new StringInput($string);
        $input->setInteractive(false);
        return $input;
    }

    private function createTestApplication()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => [
                'driver' => 'pdo_sqlite',
                'path' => __DIR__ . '/sqlite.db',
            ],
        ));
        $app->register(new DoctrineOrmServiceProvider(), [
            'orm.em.options' => [
                'mappings' => [
                    [
                        'type' => 'annotation',
                        'namespace' => 'Bezhanov\Silex\AliceDataFixtures\Tests\Entity',
                        'path' => __DIR__ . '/Entity',
                        'use_simple_annotation_reader' => false,
                    ],
                ],
            ],
        ]);
        $app->register(new FixturesServiceProvider(new Console()));
        return $app;
    }

    private function assertFixturesHaveBeenLoaded(Application $application, $isAppend = false)
    {
        $multiplier = $isAppend ? 2 : 1;
        $foo = $application['orm.em']->getRepository(Foo::class)->findAll();
        $bar = $application['orm.em']->getRepository(Bar::class)->findAll();
        $this->assertEquals(10 * $multiplier, count($foo));
        $this->assertEquals(5 * $multiplier, count($bar));
    }
}

class TestOutput extends Output
{
    public $output = '';

    public function clear()
    {
        $this->output = '';
    }

    protected function doWrite($message, $newline)
    {
        $this->output .= $message.($newline ? "\n" : '');
    }
}