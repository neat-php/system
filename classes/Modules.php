<?php

namespace Neat\System;

use Neat\Service\Container;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;

class Modules
{
    /** @var Container */
    protected $container;

    /** @var string[] */
    protected $classes;

    /** @var string[] */
    protected $paths;

    /** @var object[] */
    protected $modules;

    /**
     * Constructor
     *
     * @param Container $container
     * @param string[]  $classes
     * @param string[]  $paths
     */
    public function __construct(Container $container, array $classes = [], array $paths = [])
    {
        $this->container = $container;
        $this->classes   = $classes;
        $this->paths     = $paths;
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
     * Get module paths indexed by name
     *
     * @return string[]
     */
    public function paths()
    {
        array_map([$this, 'path'], $this->names());

        return $this->paths;
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
     * Get module class
     * @param string $name
     * @return string
     */
    public function class(string $name)
    {
        if (!isset($this->classes[$name])) {
            throw new ModuleNotFoundException("Module not found: {$name}");
        }

        return $this->classes[$name];
    }

    /**
     * Get module path
     *
     * @param string $name
     * @return string
     * @throws ReflectionException
     */
    public function path(string $name)
    {
        return $this->paths[$name]
            ?? $this->paths[$name] = dirname((new ReflectionClass($this->class($name)))->getFileName());
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
