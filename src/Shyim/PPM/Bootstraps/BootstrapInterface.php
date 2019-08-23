<?php

namespace Shyim\PPM\Bootstraps;

/**
 * All application bootstraps must implement this interface
 */
interface BootstrapInterface
{
    public function getApplication();
}