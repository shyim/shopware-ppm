<?php

namespace Shyim\PPM\Bootstraps;

use PHPPM\Bootstraps\ApplicationEnvironmentAwareInterface;
use PHPPM\Bootstraps\BootstrapInterface;
use PHPPM\Bootstraps\HooksInterface;
use PHPPM\Utils;

/**
 * A default bootstrap for the Shopware
 */
class Shopware implements BootstrapInterface, HooksInterface, ApplicationEnvironmentAwareInterface
{
    /**
     * @var string|null The application environment
     */
    protected $appenv;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * Instantiate the bootstrap, storing the $appenv
     *
     * @param $appenv
     * @param $debug
     */
    public function initialize($appenv, $debug)
    {
        $this->appenv = $appenv;
        $this->debug = $debug;
    }

    /**
     * Create a Symfony application
     *
     * @return \AppKernel
     */
    public function getApplication()
    {
        // include applications autoload
        $appAutoLoader = './app/autoload.php';
        if (file_exists($appAutoLoader)) {
            require $appAutoLoader;
        } else {
            require './vendor/autoload.php';
        }

        //since we need to change some services, we need to manually change some services
        $app = new \AppKernel($this->appenv, $this->debug);

        Utils::bindAndCall(function() use ($app) {
            // boot shopware
            $app->boot();
        }, $app);

        Utils::bindAndCall(function() use ($app) {
        }, $app);

        set_error_handler(function() {
            return;
        }, E_ALL);

        return $app;
    }

    /**
     * Does some necessary preparation before each request.
     *
     * @param \AppKernel $app
     */
    public function preHandle($app)
    {
    }

    /**
     * Does some necessary clean up after each request.
     *
     * @param \AppKernel $app
     */
    public function postHandle($app)
    {
        $container = $app->getContainer();

        // Doctrine crashed and needs restart
        if (!$container->get('models')->isOpen()) {
            $container->reset('models');
            $container->reset('dbal_connection');
        }

        // Remove session
        if ($container->initialized('session')) {
            $container->reset('session');
        }

        // Remove backend session
        if ($container->initialized('backendsession')) {
            $container->reset('backendsession');
        }

        // Remove container assigned shop
        if ($container->initialized('shop')) {
            $container->reset('shop');
        }


        if ($container->initialized('front')) {
            Utils::hijackProperty($container->get('front'), 'request', null);
            Utils::hijackProperty($container->get('front'), 'response', null);
        }
    }
}
