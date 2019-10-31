<?php

namespace Neat\System;

use Neat\Service\Container;
use Throwable;

/**
 * System Kernel class
 */
class Kernel
{
    /** @var array */
    const PLUGINS = [];

    /** @var Container */
    protected $services;

    /** @var string[] */
    protected $plugins = [];

    /** @var object[] */
    protected $objects = [];

    /**
     * Constructor
     *
     * @param Container|null $services
     * @param string[]       $plugins
     */
    public function __construct(Container $services = null, array $plugins = null)
    {
        $this->services = $services ?? new Container();
        foreach ($plugins ?? static::PLUGINS as $plugin) {
            $this->add($plugin);
        }
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
     * Get all plugins
     *
     * @return array
     */
    public function all(): array
    {
        return $this->plugins;
    }

    /**
     * Add plugin
     *
     * @param string $plugin
     */
    public function add(string $plugin)
    {
        $this->plugins[] = $plugin;
    }

    /**
     * Has plugin?
     *
     * @param string $plugin
     * @return bool
     */
    public function has(string $plugin)
    {
        return in_array($plugin, $this->plugins);
    }

    /**
     * Insert plugin
     *
     * @param string $plugin
     * @param int    $offset
     */
    public function insert($plugin, int $offset = 0)
    {
        array_splice($this->plugins, $offset, 0, [$plugin]);
    }

    /**
     * Remove plugin
     *
     * @param string $plugin
     */
    public function remove($plugin)
    {
        array_splice($this->plugins, $this->offset($plugin), 1);
    }

    /**
     * Replace plugin
     *
     * @param string $plugin
     * @param string $replacement
     */
    public function replace($plugin, $replacement)
    {
        array_splice($this->plugins, $this->offset($plugin), 1, [$replacement]);
    }

    /**
     * Get plugin offset
     *
     * @param string $plugin
     * @return int
     */
    public function offset($plugin): int
    {
        $offset = array_search($plugin, $this->plugins);
        if ($offset === false) {
            throw new PluginNotFoundException('Plugin not found: ' . $plugin);
        }

        return $offset;
    }

    /**
     * Get plugin offset to insert before
     *
     * @param string $plugin
     * @return int
     */
    public function before($plugin): int
    {
        return $this->offset($plugin);
    }

    /**
     * Get plugin offset to insert after
     *
     * @param string $plugin
     * @return int
     */
    public function after($plugin): int
    {
        return $this->offset($plugin) + 1;
    }

    /**
     * Get plugin objects
     *
     * @return iterable|object[]
     */
    public function objects()
    {
        foreach ($this->plugins as $plugin) {
            yield $this->objects[$plugin]
                ?? $this->objects[$plugin] = $this->services->get($plugin);
        }
    }

    /**
     * Bootstrap before handling the request
     */
    private function bootstrap()
    {
        foreach ($this->objects() as $object) {
            if ($object instanceof Bootstrap) {
                $object->bootstrap();
            }
        }
    }

    /**
     * Terminate after handling the request
     */
    private function terminate()
    {
        foreach ($this->objects() as $object) {
            if ($object instanceof Terminate) {
                $object->terminate();
            }
        }
    }

    /**
     * Handle the request
     */
    private function handle()
    {
        foreach ($this->objects() as $object) {
            if ($object instanceof Handle) {
                $object->handle();
            }
        }
    }

    /**
     * Handle a failure
     *
     * @param Throwable $exception
     */
    private function fail(Throwable $exception)
    {
        $this->services->set(Throwable::class, $exception);

        foreach ($this->objects() as $object) {
            if ($object instanceof Fail) {
                $object->fail($exception);
            }
        }
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
        $kernel = new static();
        $kernel->run();
    }
}
