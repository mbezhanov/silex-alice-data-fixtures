<?php

namespace Bezhanov\Silex\AliceDataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;

class GenericAliceFixture implements FixtureInterface
{
    private $loader;

    /**
     * @var string A YML file containing the entity declarations
     */
    private $fixture;

    public function __construct(NativeLoader $loader, string $fixture)
    {
        $this->loader = $loader;
        $this->fixture = $fixture;
    }

    public function load(ObjectManager $manager)
    {
        $objectSet = $this->loader->loadFile($this->fixture);

        foreach ($objectSet->getObjects() as $object) {
            $manager->persist($object);
        }
        $manager->flush();
    }
}
