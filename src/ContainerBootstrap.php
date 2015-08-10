<?php
/**
 * An extension to Aura.Di to integrate it with Slim 3
 *
 * @link      https://github.com/ackeephp/auradi-slim-container
 * @copyright Copyright © 2015 Andrew Smith
 * @license   https://github.com/ackee/auradi-slim-container/LICENSE (MIT License)
 */
namespace Ackee\AuraDiSlimContainer;

use Aura\Di\Container;
use Aura\Di\ContainerBuilder;

class ContainerBootstrap
{
    /**
     * @return \Aura\Di\Container
     */
    public static function setup($autoResolve = false)
    {
        $builder = new ContainerBuilder();
        $di = $builder->newInstance($autoResolve);
        $config = $di->newInstance('Ackee\AuraDiSlimContainer\SlimConfig');
        $config->define($di);

        if ($autoResolve) {
            static::setupInterfaces($di);
        }

        return $di;
    }

    private static function setupInterfaces(Container $di)
    {
        $di->types[\Psr\Http\Message\RequestInterface::class] = $di->lazyGet('request');
        $di->types[\Psr\Http\Message\ResponseInterface::class] = $di->lazyGet('response');
        $di->types[\Slim\Interfaces\Http\EnvironmentInterface::class] = $di->lazyGet('environment');
        $di->types[\Slim\Interfaces\RouterInterface::class] = $di->lazyGet('router');
        $di->types[\Slim\Http\Request::class] = $di->lazyGet('request');
        $di->types[\Slim\Http\Response::class] = $di->lazyGet('response');
    }
}