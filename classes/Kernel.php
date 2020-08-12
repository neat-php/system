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
    const TERMINATORS   = [];
    const HANDLERS      = [];
    const FAILERS       = [];

    /** @var Container */
    protected $services;

    /** @var Handler */
    protected $bootstrappers;

    /** @var Handler */
    protected $terminators;

    /** @var Handler */
    protected $handlers;

    /** @var Handler */
    protected $failers;

    /**
     * Constructor
     *
     * @param Container $services
     */
    public function __construct(Container $services)
    {
        $this->services = $services;

        $this->bootstrappers = new Handler(static::BOOTSTRAPPERS);
        $this->terminators   = new Handler(static::TERMINATORS);
        $this->handlers      = new Handler(static::HANDLERS);
        $this->failers       = new Handler(static::FAILERS);
    }

    /**
     * Get bootstrappers
     *
     * @return Handler
     */
    public function bootstrappers(): Handler
    {
        return $this->bootstrappers;
    }

    /**
     * Get terminators
     *
     * @return Handler
     */
    public function terminators(): Handler
    {
        return $this->terminators;
    }

    /**
     * Get handlers
     *
     * @return Handler
     */
    public function handlers(): Handler
    {
        return $this->handlers;
    }

    /**
     * Get failers
     *
     * @return Handler
     */
    public function failers(): Handler
    {
        return $this->failers;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Call given handlers
     *
     * @param Handler $handlers
     */
    private function call(Handler $handlers)
    {
        foreach ($handlers->all() as $handler) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->services->call($handler);
        }
    }

    /**
     * Bootstrap before handling the request
     */
    private function bootstrap()
    {
        $this->call($this->bootstrappers);
    }

    /**
     * Terminate after handling the request
     */
    private function terminate()
    {
        $this->call($this->terminators);
    }

    /**
     * Handle the request
     */
    private function handle()
    {
        $this->call($this->handlers);
    }

    /**
     * Handle a failure
     *
     * @param Throwable $exception
     */
    private function fail(Throwable $exception)
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
            $this->bootstrap();
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
        $services = new Container();
        $services->set(Container::class, $services);

        $kernel = new static($services);
        $kernel->run();
    }
}
