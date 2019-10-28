<?php

namespace Neat\System\Test;

use Neat\System\Handler;
use Neat\System\HandlerNotFoundException;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /**
     * Test empty
     */
    public function testEmpty()
    {
        $handler = new Handler();

        $this->assertSame([], $handler->all());
        $this->assertFalse($handler->has(CallableMock::class));
    }

    /**
     * Test initialized
     */
    public function testInitialized()
    {
        $handlers = new Handler([CallableMock::class]);

        $this->assertSame([CallableMock::class], $handlers->all());
        $this->assertTrue($handlers->has(CallableMock::class));
    }

    /**
     * Test insert
     */
    public function testInsert()
    {
        $handler = new Handler([CallableMock::class]);

        $handler->insert('x');
        $this->assertSame(['x', CallableMock::class], $handler->all());

        $handler->insert('y', 1);
        $this->assertSame(['x', 'y', CallableMock::class], $handler->all());

        $handler->insert('z', 3);
        $this->assertSame(['x', 'y', CallableMock::class, 'z'], $handler->all());

        $handler->insert('z', 10);
        $this->assertSame(['x', 'y', CallableMock::class, 'z', 'z'], $handler->all());
    }

    /**
     * Test add
     */
    public function testAdd()
    {
        $handler = new Handler([CallableMock::class]);
        $handler->add('x');

        $this->assertTrue($handler->has(CallableMock::class));
        $this->assertTrue($handler->has('x'));
        $this->assertSame([CallableMock::class, 'x'], $handler->all());
    }

    /**
     * Test offset
     */
    public function testOffset()
    {
        $handler = new Handler(['x', 'y', 'z']);

        $this->assertSame(0, $handler->offset('x'));
        $this->assertSame(1, $handler->offset('y'));
        $this->assertSame(2, $handler->offset('z'));
    }

    /**
     * Test before offset
     */
    public function testBefore()
    {
        $handler = new Handler(['x', 'y', 'z']);

        $this->assertSame(0, $handler->before('x'));
        $this->assertSame(1, $handler->before('y'));
        $this->assertSame(2, $handler->before('z'));
    }

    /**
     * Test before unknown
     */
    public function testBeforeUnknown()
    {
        $this->expectException(HandlerNotFoundException::class);
        $this->expectExceptionMessage('Handler not found: unknown');

        $handler = new Handler(['x', 'y', 'z']);
        $handler->before('unknown');
    }

    /**
     * Test after offset
     */
    public function testAfter()
    {
        $handler = new Handler(['x', 'y', 'z']);

        $this->assertSame(1, $handler->after('x'));
        $this->assertSame(2, $handler->after('y'));
        $this->assertSame(3, $handler->after('z'));
    }

    /**
     * Test after unknown
     */
    public function testAfterUnknown()
    {
        $this->expectException(HandlerNotFoundException::class);
        $this->expectExceptionMessage('Handler not found: unknown');

        $handler = new Handler(['x', 'y', 'z']);
        $handler->after('unknown');
    }
}
