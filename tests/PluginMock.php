<?php

namespace Neat\System\Test;

use Neat\System\Bootstrap;
use Neat\System\Fail;
use Neat\System\Handle;
use Neat\System\Terminate;
use Throwable;

class PluginMock implements Bootstrap, Handle, Terminate, Fail
{
    /**
     * Bootstrap plugin
     */
    public function bootstrap()
    {
    }

    /**
     * Failure handler plugin
     *
     * @param Throwable $throwable
     */
    public function fail(Throwable $throwable)
    {
    }

    /**
     * Handle plugin
     */
    public function handle()
    {
    }

    /**
     * Terminate plugin
     */
    public function terminate()
    {
    }
}