## Aura.Di Slim Container

### Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Aura.Di Slim Container.

```bash
composer require collective/aura-di-slim
```

## Usage

#### Basic Setup

```php
$container = Collective\AuraDiSlim\ContainerBootstrap::setup();
$app = new Slim\App($container);
```

#### Advanced Setup with auto-wiring

```php
$container = Collective\AuraDiSlim\ContainerBootstrap::setup(true);
$app = new Slim\App($container);
```

## Credits

- [Andrew Smith](https://github.com/silentworks)

## License

Aura.Di Slim Container is licensed under the MIT license. See [License File](LICENSE.md) for more information.