<?php

namespace Shyim\PPM\Bootstraps;

interface HooksInterface
{
    public function preHandle($app);
    public function postHandle($app);
}