# silex-alice-data-fixtures

A library providing simple integration of [nelmio/alice](https://github.com/nelmio/alice) and [doctrine/data-fixtures](https://github.com/doctrine/data-fixtures) into Silex projects

## Installation

Install the library through Composer:

```bash
composer require bezhanov/silex-alice-data-fixtures
```

## Usage

To get up and running, register all necessary Service Providers with your Application by following the example below:

```php
<?php 

use Bezhanov\Silex\AliceDataFixtures\FixturesServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\Console\Application as Console;

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
                'namespace' => 'App\Entity',
                'path' => __DIR__ . '/src/App/Entity',
                'use_simple_annotation_reader' => false,
            ],
        ],
    ],
]);

$console = new Console();
$app->register(new FixturesServiceProvider($console));

$app->boot();
$console->run();
```

This will automatically register the ```fixtures:load``` command with your Console application, and you will be able to call:

```bash
./bin/console fixtures:load
```

By default, the command will try to load the "fixtures.yml" file located in your current working directory. You can specify the full path to a fixtures file by issuing:

```bash
./bin/console fixtures:load --fixture="/path/to/fixture.yml"
```

If you'd like to append fixtures to your existing data, instead of truncating your database, you can use:

```bash
./bin/console fixtures:load --fixture="/path/to/fixture.yml" --append
```

The YML files follow the standard format recognized by Alice:

```yml
Bezhanov\Silex\AliceDataFixtures\Tests\Entity\Foo:
    foo{1..10}:
        name: '<name()>'
Bezhanov\Silex\AliceDataFixtures\Tests\Entity\Bar:
    bar{1..5}:
        name: '<name()>'
```

## Contributing

This library is in its early stages of development, and all contributions are welcome. Before opening PRs, make sure that all tests are passing, and that code coverage is satisfactory:

```bash
phpunit tests --coverage-html coverage
```
