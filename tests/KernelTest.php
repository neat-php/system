<?php

namespace Neat\System\Test;

use Neat\Service\Container;
use Neat\System\Kernel;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    /**
     * Test empty
     */
    public function testEmpty()
    {
        $kernel = new Kernel();

        $this->assertInstanceOf(Container::class, $kernel->services());
    }

    /**
     * Test services instance
     */
    public function testServices()
    {
        $services = new Container();
        $kernel   = new Kernel($services);

        $this->assertSame($services, $kernel->services());
    }

    public function testBootstrap()
    {
        $kernel = new Kernel();

    }

    public function testFail()
    {
        $kernel = new Kernel();

    }

    public function testTerminate()
    {
        $kernel = new Kernel();

    }

    public function testHandle()
    {
        $callable = $this->createPartialMock(CallableMock::class, ['__invoke']);
        $callable->expects($this->once())->method('__invoke')->with();

        $kernel = new Kernel();
        $kernel->handle();
    }

    public function testRun()
    {
        $kernel = new Kernel();

    }

    /**
     * Test booting up a new kernel
     */
    public function testBoot()
    {
        Kernel::boot();

        $this->addToAssertionCount(1);
    }
}
