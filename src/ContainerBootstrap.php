<?php

namespace Collective\AuraDiSlim;

use Aura\Di\Container;
use Aura\Di\ContainerBuilder;

class ContainerBootstrap
{
    /**
     * @return \Aura\Di\Container
     */
    public static function setup($autoResolve = false, $userSettings = [])
    {
        $builder = new ContainerBuilder();
        $di = $builder->newInstance($autoResolve);
        $di->values['userSettings'] = $userSettings;
        $config = $di->newInstance('Collective\AuraDiSlim\SlimConfig');
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