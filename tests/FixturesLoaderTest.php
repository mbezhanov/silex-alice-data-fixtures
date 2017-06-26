<?php

namespace Bezhanov\Silex\AliceDataFixtures\Tests;

use Bezhanov\Silex\AliceDataFixtures\FixturesLoader;
use Doctrine\Common\DataFixtures\Loader as DoctrineLoader;
use PHPUnit\Framework\TestCase;

class FixturesLoaderTest extends TestCase
{
    public function testLoadUnreadableFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $fixturesLoader = new FixturesLoader();
        $fixturesLoader->loadFromFile('/foo/bar');
    }

    public function testLoadMissingDirectory()
    {
        $this->expectException(\InvalidArgumentException::class);
        $fixturesLoader = new FixturesLoader();
        $fixturesLoader->loadFromDirectory('/foo/bar');
    }

    public function testGetFixtures()
    {
        $expectedResponse = ['foo', 'bar'];
        $doctrineLoader = $this->prophesize(DoctrineLoader::class);
        $doctrineLoader->getFixtures()->shouldBeCalledTimes(1)->willReturn($expectedResponse);
        $fixturesLoader = new FixturesLoader($doctrineLoader->reveal());
        $this->assertEquals($expectedResponse, $fixturesLoader->getFixtures());
    }
}
