<?php

namespace Neat\System\Test;

use Neat\Service\Container;
use Neat\System\Kernel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class KernelTest extends TestCase
{
    /**
     * @return Container|MockObject
     */
    protected function services(): Container
    {
        /** @var Container $services */
        $services = $this->getMockBuilder(Container::class)->getMock();

        return $services;
    }

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
        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('call')
            ->with(CallableMock::class);

        $kernel = new Kernel($services);
        $kernel->bootstrappers()->add(CallableMock::class);
        $kernel->run();
    }

    public function testFail()
    {
        $services = $this->services();

        $kernel = new Kernel($services);
        $kernel->failers()->add(CallableMock::class);
        $kernel->run();

        $services
            ->expects($this->once())
            ->method('call')
            ->with(CallableMock::class);

        $kernel->handlers()->add(function () {
            throw new RuntimeException('Failed!');
        });
        $kernel->run();
    }

    public function testTerminate()
    {
        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('call')
            ->with(CallableMock::class);

        $kernel = new Kernel($services);
        $kernel->terminators()->add(CallableMock::class);
        $kernel->run();
    }

    public function testHandle()
    {
        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('call')
            ->with(CallableMock::class);

        $kernel = new Kernel($services);
        $kernel->handlers()->add(CallableMock::class);
        $kernel->run();
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
