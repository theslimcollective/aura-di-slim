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
        'cookieLifetime' => '20 minutes',
        'cookiePath' => '/',
        'cookieDomain' => null,
        'cookieSecure' => false,
        'cookieHttpOnly' => false,
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
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
        $defaultSettings = $this->defaultSettings;

        $di->set('settings', function () use ($di, $defaultSettings) {
            $userSettings = $di->has('userSettings') ? $di->get('userSettings') : [];
            return array_merge($defaultSettings, $userSettings);
        });

        /**
         * This service MUST return a shared instance
         * of \Slim\Interfaces\Http\EnvironmentInterface.
         *
         * @return EnvironmentInterface
         */
        $di->set(
            'environment',
            $di->lazyNew(
                'Slim\Http\Environment',
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
        $di->set('router', $di->lazyNew('Slim\Router'));

        /**
         * This service MUST return a SHARED instance
         * of \Slim\Interfaces\InvocationStrategyInterface.
         *
         * @return InvocationStrategyInterface
         */
        $di->set('foundHandler', $di->lazyNew('Slim\Handlers\Strategies\RequestResponse'));

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
        $di->set('errorHandler', $di->lazyNew('Slim\Handlers\Error'));

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
        $di->set('notFoundHandler', $di->lazyNew('Slim\Handlers\NotFound'));

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
        $di->set('notAllowedHandler', $di->lazyNew('Slim\Handlers\NotAllowed'));

        /**
         * This service MUST return a NEW instance of
         * \Slim\Interfaces\CallableResolverInterface
         *
         * @return CallableResolverInterface
         */
        $di->set('callableResolver', $di->newInstance('Slim\CallableResolver', [
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