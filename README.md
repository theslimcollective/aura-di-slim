## Aura.Di Slim Container

### Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Aura.Di Slim Container.

```bash
composer require ackee/aura-di-slim-container
```

## Usage

#### Basic Setup

```php
$container = Ackee\AuraDiSlimContainer\ContainerBootstrap::setup();
$app = new Slim\App($container);
```

#### Advanced Setup with auto-wiring

```php
$container = Ackee\AuraDiSlimContainer\ContainerBootstrap::setup(true);
$app = new Slim\App($container);
```

## Credits

- [Andrew Smith](https://github.com/silentworks)

## License

Aura.Di Slim Container is licensed under the MIT license. See [License File](LICENSE.md) for more information.