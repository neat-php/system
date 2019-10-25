<?php

namespace Neat\System\Test;

use Neat\System\Handlers;
use PHPUnit\Framework\TestCase;

class HandlersTest extends TestCase
{
    /**
     * Test empty
     */
    public function testEmpty()
    {
        $handler = new Handlers();

        $this->assertSame([], $handler->all());
        $this->assertFalse($handler->has(CallableMock::class));
    }

    /**
     * Test initialized
     */
    public function testInitialized()
    {
        $handler = new Handlers([CallableMock::class]);

        $this->assertSame([CallableMock::class], $handler->all());
        $this->assertTrue($handler->has(CallableMock::class));
    }

    /**
     * Test prepend
     */
    public function testPrepend()
    {
        $handler = new Handlers([CallableMock::class]);
        $handler->prepend('x');

        $this->assertTrue($handler->has(CallableMock::class));
        $this->assertTrue($handler->has('x'));
        $this->assertSame(['x', CallableMock::class], $handler->all());
    }

    /**
     * Test append
     */
    public function testAppend()
    {
        $handler = new Handlers([CallableMock::class]);
        $handler->append('x');

        $this->assertTrue($handler->has(CallableMock::class));
        $this->assertTrue($handler->has('x'));
        $this->assertSame([CallableMock::class, 'x'], $handler->all());
    }
}
