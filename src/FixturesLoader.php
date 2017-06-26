<?php

namespace Bezhanov\Silex\AliceDataFixtures;

use Doctrine\Common\DataFixtures\Loader as DoctrineLoader;
use Faker\Generator as FakerGenerator;
use Nelmio\Alice\Loader\NativeLoader;

/**
 * @method DoctrineLoader getDoctrineLoader()
 */
class FixturesLoader extends NativeLoader
{
    private $doctrineLoader;

    public function __construct(DoctrineLoader $doctrineLoader = null, FakerGenerator $fakerGenerator = null)
    {
        $this->doctrineLoader = (null === $doctrineLoader) ? $this->getDoctrineLoader() : $doctrineLoader;
        parent::__construct($fakerGenerator);
    }

    /**
     * Scans a given directory for YML files containing fixtures, and loads them.
     *
     * @param string $dir
     */
    public function loadFromDirectory(string $dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist', $dir));
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $this->loadFromIterator($iterator);
    }

    /**
     * Loads fixture data from a given YML file
     *
     * @param string $fileName
     */
    public function loadFromFile(string $fileName)
    {
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException(sprintf('"%s" does not exist or is not readable', $fileName));
        }
        $iterator = new \ArrayIterator(array(new \SplFileInfo($fileName)));
        $this->loadFromIterator($iterator);
    }

    /**
     * Load fixtures from YML files contained in iterator.
     *
     * @param \Iterator $iterator Iterator over files from which fixtures should be loaded.
     */
    private function loadFromIterator(\Iterator $iterator)
    {
        /* @var \SplFileInfo[] $iterator */
        foreach ($iterator as $file) {
            if ($file->getFilename() === 'fixtures.yml') {
                $this->addFixture($file->getPathname());
            }
        }
    }

    protected function addFixture(string $fileName)
    {
        $this->doctrineLoader->addFixture(new GenericAliceFixture($this, $fileName));
    }

    public function getFixtures()
    {
        return $this->doctrineLoader->getFixtures();
    }

    protected function createDoctrineLoader(): DoctrineLoader
    {
        return new DoctrineLoader();
    }
}
