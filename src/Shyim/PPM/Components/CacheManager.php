<?php

namespace Shyim\PPM\Components;


use Shopware\Components\DependencyInjection\Container;

class CacheManager extends \Shopware\Components\CacheManager
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var bool
     */
    private $hasClearedCache = false;

    /**
     * CacheManager constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    public function clearTemplateCache()
    {
        parent::clearTemplateCache();
        $this->hasClearedCache = true;
    }

    public function clearConfigCache()
    {
        parent::clearConfigCache();
        $this->hasClearedCache = true;
    }

    public function clearProxyCache()
    {
        parent::clearProxyCache();
        $this->hasClearedCache = true;
    }

    public function clearOpCache()
    {
        parent::clearOpCache();
        $this->hasClearedCache = true;
    }

    /**
     * @return bool
     */
    public function hasClearedCache()
    {
        return $this->hasClearedCache;
    }
}