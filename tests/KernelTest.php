<?php

namespace Neat\System\Test;

use Neat\Service\Container;
use Neat\System\Kernel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

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
     * Test services instance
     */
    public function testServices()
    {
        $services = new Container();
        $kernel   = new Kernel($services);

        $this->assertSame($services, $kernel->services());
    }

    /**
     * Test bootstrap
     */
    public function testBootstrap()
    {
        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('call')
            ->with(CallableMock::class . '@__invoke');

        $kernel = new Kernel($services);
        $kernel->bootstrappers()->add(CallableMock::class);
        $kernel->run();
    }

    /**
     * Test handle
     */
    public function testHandle()
    {
        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('call')
            ->with(CallableMock::class . '@__invoke');

        $kernel = new Kernel($services);
        $kernel->handlers()->add(CallableMock::class);
        $kernel->run();
    }

    /**
     * Test terminate
     */
    public function testTerminate()
    {
        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('call')
            ->with(CallableMock::class . '@__invoke');

        $kernel = new Kernel($services);
        $kernel->terminators()->add(CallableMock::class);
        $kernel->run();
    }

    /**
     * Test fail
     */
    public function testFail()
    {
        $failure = function (): RuntimeException {
            throw new RuntimeException('Failed!');
        };

        $services = $this->services();
        $services
            ->expects($this->at(0))
            ->method('call')
            ->with($failure)
            ->willThrowException($exception = new RuntimeException('Failed!'));
        $services
            ->expects($this->at(1))
            ->method('set')
            ->with(Throwable::class, $exception);
        $services
            ->expects($this->at(2))
            ->method('call')
            ->with(CallableMock::class . '@__invoke');

        $kernel = new Kernel($services);
        $kernel->failers()->add(CallableMock::class);
        $kernel->handlers()->add($failure);
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
