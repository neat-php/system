<?php

namespace Neat\System;

class Handlers
{
    /** @var callable[] */
    protected $handlers;

    /**
     * Handler constructor
     *
     * @param callable[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Get all handlers
     *
     * @return array|callable[]
     */
    public function all()
    {
        return $this->handlers;
    }

    /**
     * Has handler?
     *
     * @param callable|string $handler
     * @return bool
     */
    public function has($handler): bool
    {
        return in_array($handler, $this->handlers);
    }

    /**
     * Prepend handler
     *
     * @param callable|string $handler
     */
    public function prepend($handler)
    {
        array_unshift($this->handlers, $handler);
    }

    /**
     * Append handler
     *
     * @param callable|string $handler
     */
    public function append($handler)
    {
        array_push($this->handlers, $handler);
    }
}
