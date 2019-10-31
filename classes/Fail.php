<?php

namespace Neat\System;

use Throwable;

interface Fail
{
    /**
     * Failure handler plugin
     *
     * @param Throwable $throwable
     */
    public function fail(Throwable $throwable);
}
