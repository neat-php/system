<?php

namespace Neat\System;

use Neat\Service\Container;
use ReflectionFunction;

class Modules
{
    /** @var Container */
    protected $container;

    /** @var string[] */
    protected $classes;

    /** @var object[] */
    protected $modules;

    /**
     * Constructor
     *
     * @param Container $container
     * @param string[]  $classes
     */
    public function __construct(Container $container, array $classes = [])
    {
        $this->container = $container;
        $this->classes   = $classes;
        $this->modules   = [];
    }

    /**
     * Get module names
     *
     * @return string[]
     */
    public function names()
    {
        return array_keys($this->classes);
    }

    /**
     * Get module classes indexed by name
     *
     * @return string[]
     */
    public function classes()
    {
        return $this->classes;
    }

    /**
     * Get all modules
     *
     * @return object[]
     */
    public function all()
    {
        array_map([$this, 'get'], $this->names());

        return $this->modules;
    }

    /**
     * Get modules implementing interface
     *
     * @param string $interface
     * @return object[]
     */
    public function implementing(string $interface)
    {
        return array_filter($this->all(), function ($module) use ($interface) {
            return $module instanceof $interface;
        });
    }

    /**
     * Has module?
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->classes[$name]);
    }

    /**
     * Get a module by name
     *
     * @param string $name
     * @return object
     */
    public function get(string $name)
    {
        if (!isset($this->classes[$name])) {
            throw new ModuleNotFoundException("Module not found: {$name}");
        }

        return $this->modules[$name]
            ?? $this->modules[$name] = $this->container->getOrCreate($this->classes[$name]);
    }

    /**
     * Call a closure for each module that fits its first parameter
     *
     * @note The passed callable must not require any additional parameters
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback)
    {
        $reflection = new ReflectionFunction($callback);
        $interface  = $reflection->getNumberOfParameters()
                    ? $reflection->getParameters()[0]->getClass()->name ?? null
                    : null;

        return array_map($callback, $interface ? $this->implementing($interface) : $this->all());
    }
}
