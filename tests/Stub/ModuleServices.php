<?php

namespace Neat\System\Test\Stub;

use Neat\Service\Container;
use Neat\System\Services;

class ModuleServices
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function services(Services $module)
    {
        $module->services($this->container);
    }
}
