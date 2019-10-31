<?php

namespace Neat\System;

use Neat\Service\Container;

interface Services
{
    /**
     * Register services
     *
     * @param Container $container
     */
    public function services(Container $container);
}
