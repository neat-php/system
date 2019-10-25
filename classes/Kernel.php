<?php

namespace Neat\System;

use Neat\Service\Container;
use Throwable;

/**
 * System Kernel class
 */
class Kernel
{
    const BOOTSTRAPPERS = [];
    const TERMINATORS = [];
    const HANDLER = [];
    const FAILER = [];

    /** @var Container */
    protected $services;

    /** @var Handlers */
    protected $bootstrappers;

    /** @var Handlers */
    protected $terminators;

    /** @var Handlers */
    protected $handlers;

    /** @var Handlers */
    protected $failers;

    /**
     * Constructor
     *
     * @param Container|null $services
     */
    public function __construct(Container $services = null)
    {
        $this->services      = $services ?? new Container();
        $this->bootstrappers = new Handlers(self::BOOTSTRAPPERS);
        $this->terminators   = new Handlers(self::TERMINATORS);
        $this->handlers      = new Handlers(self::HANDLERS);
        $this->failers       = new Handlers(self::FAILERS);
    }

    /**
     * Get service container
     *
     * @return Container
     */
    public function services(): Container
    {
        return $this->services;
    }

    /**
     * Get bootstrappers
     *
     * @return Handlers
     */
    public function bootstrappers(): Handlers
    {
        return $this->bootstrappers;
    }

    /**
     * Get terminators
     *
     * @return Handlers
     */
    public function terminators(): Handlers
    {
        return $this->terminators;
    }

    /**
     * Get handlers
     *
     * @return Handlers
     */
    public function handlers(): Handlers
    {
        return $this->handlers;
    }

    /**
     * Get failers
     *
     * @return Handlers
     */
    public function failers(): Handlers
    {
        return $this->failers;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Call given handlers
     *
     * @param Handlers $handlers
     */
    private function call(Handlers $handlers)
    {
        foreach ($handlers->all() as $handler) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->services->call($handler);
        }
    }

    /**
     * Bootstrap before handling the request
     */
    public function bootstrap()
    {
        $this->call($this->bootstrappers);
    }

    /**
     * Terminate after handling the request
     */
    public function terminate()
    {
        $this->call($this->terminators);
    }

    /**
     * Handle the request
     */
    public function handle()
    {
        $this->call($this->handlers);
    }

    /**
     * Handle a failure
     *
     * @param Throwable $exception
     */
    public function fail(Throwable $exception)
    {
        $this->services->set(Throwable::class, $exception);

        $this->call($this->failers);
    }

    /**
     * Run the kernel
     */
    public function run()
    {
        try {
            ($this->bootstrappers)->all();
            $this->handle();
            $this->terminate();
        } catch (Throwable $exception) {
            $this->fail($exception);
        }
    }

    /**
     * Boot up a new kernel instance
     */
    public static function boot()
    {
        $kernel = new static();
        $kernel->run();
    }
}
