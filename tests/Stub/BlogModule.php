<?php

namespace Neat\System\Test\Stub;

use Neat\Service\Container;
use Neat\System\Services;

class BlogModule implements Services
{
    /**
     * Register services
     *
     * @param Container $container
     */
    public function services(Container $container)
    {
    }

    /**
     * Do stuff
     */
    public function stuff()
    {
    }
}
