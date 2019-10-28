<?php

namespace Neat\System;

class Handler
{
    /** @var callable[] */
    protected $handlers;

    /**
     * Handler constructor
     *
     * @param array|callable[] $handlers
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
     * Add handler
     *
     * @param callable|string $handler
     */
    public function add($handler)
    {
        array_push($this->handlers, $handler);
    }

    /**
     * Insert handler
     *
     * @param callable|string $handler
     * @param int             $offset
     */
    public function insert($handler, int $offset = 0)
    {
        array_splice($this->handlers, $offset, 0, [$handler]);
    }

    /**
     * Remove handler
     *
     * @param callable|string $handler
     */
    public function remove($handler)
    {
        unset($this->handlers[$this->offset($handler)]);
    }

    /**
     * Get handler offset
     *
     * @param callable|string $handler
     * @return int
     */
    public function offset($handler): int
    {
        $offset = array_search($handler, $this->handlers);
        if ($offset === false) {
            throw new HandlerNotFoundException('Handler not found: ' . $handler);
        }

        return $offset;
    }

    /**
     * Get handler offset to insert before
     *
     * @param callable|string $handler
     * @return int
     */
    public function before($handler): int
    {
        return $this->offset($handler);
    }

    /**
     * Get handler offset to insert after
     *
     * @param callable|string $handler
     * @return int
     */
    public function after($handler): int
    {
        return $this->offset($handler) + 1;
    }
}
