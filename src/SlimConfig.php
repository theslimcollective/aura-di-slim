<?php

namespace Collective\AuraDiSlim;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\Http\EnvironmentInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouterInterface;

class SlimConfig extends ContainerConfig
{
    /**
     * Default settings
     *
     * @var array
     */
    private $defaultSettings = [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
    ];

    /**
     *
     * Define params, setters, and services before the Container is locked.
     *
     * @param Container $di The DI container.
     *
     * @return null
     *
     */
    public function define(Container $di)
    {
        $di->set('settings', $di->newInstance(\Slim\Collection::class, [
            'items' => array_merge(
                $this->defaultSettings, 
                $di->values['userSettings']
            )
        ]));

        /**
         * This service MUST return a shared instance
         * of \Slim\Interfaces\Http\EnvironmentInterface.
         *
         * @return EnvironmentInterface
         */
        $di->set(
            'environment',
            $di->lazyNew(
                \Slim\Http\Environment::class,
                [
                    'items' => $_SERVER
                ]
            )
        );

        /**
         * This service MUST return a NEW instance
         * of \Psr\Http\Message\ServerRequestInterface.
         */
        $di->set(
            'request',
            $di->lazy(
                /**
                 * @return ServerRequestInterface
                 */
                function () use ($di) {
                    /** @var Environment $environment */
                    $environment = $di->get('environment');
                    return Request::createFromEnvironment($environment);
                }
            )
        );

        /**
         * This service MUST return a NEW instance
         * of \Psr\Http\Message\ResponseInterface.
         */
        $di->set(
            'response',
            $di->lazy(
                /**
                 * @return ResponseInterface
                 */
                function () use ($di) {
                    $settings = $di->get('settings');
                    $headers = new Headers(['Content-Type' => 'text/html']);
                    $response = new Response(200, $headers);

                    return $response->withProtocolVersion($settings['httpVersion']);
                }
            )
        );

        /**
         * This service MUST return a SHARED instance
         * of \Slim\Interfaces\RouterInterface.
         *
         * @return RouterInterface
         */
        $di->set('router', $di->lazyNew(\Slim\Router::class));

        /**
         * This service MUST return a SHARED instance
         * of \Slim\Interfaces\InvocationStrategyInterface.
         *
         * @return InvocationStrategyInterface
         */
        $di->set('foundHandler', $di->lazyNew(\Slim\Handlers\Strategies\RequestResponse::class));

        /**
         * This service MUST return a callable
         * that accepts three arguments:
         *
         * 1. Instance of \Psr\Http\Message\ServerRequestInterface
         * 2. Instance of \Psr\Http\Message\ResponseInterface
         * 3. Instance of \Exception
         *
         * The callable MUST return an instance of
         * \Psr\Http\Message\ResponseInterface.
         *
         * @return callable
         */
        $di->set('errorHandler', $di->lazyNew(\Slim\Handlers\Error::class, [
            'displayErrorDetails' => $di->get('settings')['displayErrorDetails']
        ])));

        /**
         * This service MUST return a callable
         * that accepts two arguments:
         *
         * 1. Instance of \Psr\Http\Message\ServerRequestInterface
         * 2. Instance of \Psr\Http\Message\ResponseInterface
         *
         * The callable MUST return an instance of
         * \Psr\Http\Message\ResponseInterface.
         *
         * @return callable
         */
        $di->set('notFoundHandler', $di->lazyNew(\Slim\Handlers\NotFound::class));

        /**
         * This service MUST return a callable
         * that accepts three arguments:
         *
         * 1. Instance of \Psr\Http\Message\ServerRequestInterface
         * 2. Instance of \Psr\Http\Message\ResponseInterface
         * 3. Array of allowed HTTP methods
         *
         * The callable MUST return an instance of
         * \Psr\Http\Message\ResponseInterface.
         *
         * @return callable
         */
        $di->set('notAllowedHandler', $di->lazyNew(\Slim\Handlers\NotAllowed::class));

        /**
         * This service MUST return a NEW instance of
         * \Slim\Interfaces\CallableResolverInterface
         *
         * @return CallableResolverInterface
         */
        $di->set('callableResolver', $di->newInstance(\Slim\CallableResolver::class, [
            'container' => $di
        ]));
    }

    /**
     *
     * Modify service objects after the Container is locked.
     *
     * @param Container $di The DI container.
     *
     * @return null
     *
     */
    public function modify(Container $di)
    {
    }
}