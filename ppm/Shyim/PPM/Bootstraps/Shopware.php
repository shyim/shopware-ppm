<?php

namespace Shyim\PPM\Bootstraps;

// We need to patch this **** Zend_Session
require dirname(__DIR__) . '/Patch/Session.php';

use AppKernel;
use PDOException;
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
     * This services must be reseted after every request
     */
    const RESET_SERVICES = [
        'shop',
        'session',
        'backendsession',
        'auth'
    ];

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
     * @return AppKernel
     * @throws \Exception
     */
    public function getApplication()
    {
        // include applications autoload
        require './app/autoload.php';

        //since we need to change some services, we need to manually change some services
        $app = new AppKernel($this->appenv, $this->debug);

        $app->boot();

        // smarty throws many errors
        set_error_handler(function() {}, E_ALL);

        return $app;
    }

    /**
     * Does some necessary preparation before each request.
     *
     * @param AppKernel $app
     * @throws \Enlight_Event_Exception
     */
    public function preHandle($app)
    {
        \Zend_Session::$_sessionStarted = false;
        \Zend_Session::$_writeClosed = false;
        \Zend_Session::$_sessionCookieDeleted = false;
        \Zend_Session::$_destroyed = false;
        \Zend_Session::$_regenerateIdState = false;

        if ($app->getContainer()->initialized('events')) {
            $app->getContainer()->get('events')->notify('PPM_Request_preHandle', ['app' => $app]);
        }

        try {
            $app->getContainer()->get('db_connection')->query('SELECT 1');
        } catch (PDOException $e) {
            // Houston, we lost the MySQL Connection. Killing the worker is required
            exit(-1);
        }
    }

    /**
     * Does some necessary clean up after each request.
     *
     * @param AppKernel $app
     * @throws \Enlight_Event_Exception
     */
    public function postHandle($app)
    {
        $container = $app->getContainer();

        // Doctrine crashed and needs restart
        if ($container->initialized('models')) {
            $models = $container->get('models');

            if (!$models->isOpen()) {
                $container
                    ->reset('models')
                    ->reset('dbal_connection');

                $container->load('dbal_connection');
                $container->load('models');
            } else {
                $models->clear();
            }
        }

        foreach (self::RESET_SERVICES as $service) {
            if ($container->initialized($service)) {
                $container->reset($service);
            }
        }

        // Reset request and response
        if ($container->initialized('front')) {
            Utils::hijackProperty($container->get('front'), 'request', null);
            Utils::hijackProperty($container->get('front'), 'response', null);
        }

        // Lets notify plugins about request completed
        if ($app->getContainer()->initialized('events')) {
            $app->getContainer()->get('events')->notify('PPM_Request_postHandle', ['app' => $app]);
        }

        // Remove global template assigns
        if ($container->initialized('template')) {
            $container->get('template')->clearAllAssign();
        }

        \Zend_Session::$_sessionStarted = false;
        \Zend_Session::$_writeClosed = false;
        \Zend_Session::$_sessionCookieDeleted = false;
        \Zend_Session::$_destroyed = false;
        \Zend_Session::$_regenerateIdState = false;
        session_write_close();
        session_destroy();
    }
}
