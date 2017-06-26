<?php

namespace Bezhanov\Silex\AliceDataFixtures\Tests;

use Bezhanov\Silex\AliceDataFixtures\GenericAliceFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\ObjectBag;
use Nelmio\Alice\ObjectSet;
use Nelmio\Alice\ParameterBag;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GenericAliceFixtureTest extends TestCase
{
    public function testLoad()
    {
        $fixtureFile = 'fixtures.yml';
        $objects = [
            'object1' => new \stdClass(),
            'object2' => new \stdClass(),
            'object3' => new \stdClass()
        ];
        $objectSet = new ObjectSet(new ParameterBag(), new ObjectBag($objects));
        $loader = $this->prophesize(NativeLoader::class);
        $loader->loadFile($fixtureFile)->shouldBeCalledTimes(1)->willReturn($objectSet);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->persist(Argument::any())->shouldBeCalledTimes(3);
        $objectManager->flush()->shouldBeCalledTimes(1);
        $fixture = new GenericAliceFixture($loader->reveal(), $fixtureFile);
        $fixture->load($objectManager->reveal());
    }
}
