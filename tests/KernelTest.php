<?php

namespace Neat\System\Test;

use Neat\Service\Container;
use Neat\System\Bootstrap;
use Neat\System\Handle;
use Neat\System\Kernel;
use Neat\System\PluginNotFoundException;
use Neat\System\Terminate;
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
        $this->assertSame([], $kernel->all());
        $this->assertFalse($kernel->has(PluginMock::class));
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
     * Test initialized
     */
    public function testInitialized()
    {
        $kernel = new Kernel(new Container(), [PluginMock::class]);

        $this->assertSame([PluginMock::class], $kernel->all());
        $this->assertTrue($kernel->has(PluginMock::class));
    }

    /**
     * Test insert
     */
    public function testInsert()
    {
        $kernel = new Kernel(new Container(), [PluginMock::class]);

        $kernel->insert('x');
        $this->assertSame(['x', PluginMock::class], $kernel->all());

        $kernel->insert('y', 1);
        $this->assertSame(['x', 'y', PluginMock::class], $kernel->all());

        $kernel->insert('z', 3);
        $this->assertSame(['x', 'y', PluginMock::class, 'z'], $kernel->all());

        $kernel->insert('z', 10);
        $this->assertSame(['x', 'y', PluginMock::class, 'z', 'z'], $kernel->all());
    }

    /**
     * Test add
     */
    public function testAdd()
    {
        $kernel = new Kernel(new Container(), [PluginMock::class]);
        $kernel->add('x');

        $this->assertTrue($kernel->has(PluginMock::class));
        $this->assertTrue($kernel->has('x'));
        $this->assertSame([PluginMock::class, 'x'], $kernel->all());
    }

    /**
     * Test remove
     */
    public function testRemove()
    {
        $kernel = new Kernel(new Container(), ['x', 'y', 'z']);
        $kernel->remove('y');

        $this->assertSame(['x', 'z'], $kernel->all());
    }

    /**
     * Test remove
     */
    public function testReplace()
    {
        $kernel = new Kernel(new Container(), ['x', 'y', 'z']);
        $kernel->replace('y', 'replacement');

        $this->assertSame(['x', 'replacement', 'z'], $kernel->all());
    }

    /**
     * Test offset
     */
    public function testOffset()
    {
        $kernel = new Kernel(new Container(), ['x', 'y', 'z']);

        $this->assertSame(0, $kernel->offset('x'));
        $this->assertSame(1, $kernel->offset('y'));
        $this->assertSame(2, $kernel->offset('z'));
    }

    /**
     * Test before offset
     */
    public function testBefore()
    {
        $kernel = new Kernel(new Container(), ['x', 'y', 'z']);

        $this->assertSame(0, $kernel->before('x'));
        $this->assertSame(1, $kernel->before('y'));
        $this->assertSame(2, $kernel->before('z'));
    }

    /**
     * Test before unknown
     */
    public function testBeforeUnknown()
    {
        $this->expectException(PluginNotFoundException::class);
        $this->expectExceptionMessage('Plugin not found: unknown');

        $kernel = new Kernel(new Container(), ['x', 'y', 'z']);
        $kernel->before('unknown');
    }

    /**
     * Test after offset
     */
    public function testAfter()
    {
        $kernel = new Kernel(new Container(), ['x', 'y', 'z']);

        $this->assertSame(1, $kernel->after('x'));
        $this->assertSame(2, $kernel->after('y'));
        $this->assertSame(3, $kernel->after('z'));
    }

    /**
     * Test after unknown
     */
    public function testAfterUnknown()
    {
        $this->expectException(PluginNotFoundException::class);
        $this->expectExceptionMessage('Plugin not found: unknown');

        $kernel = new Kernel(new Container(), ['x', 'y', 'z']);
        $kernel->after('unknown');
    }

    /**
     * Test bootstrap
     */
    public function testBootstrap()
    {
        $bootstrap = $this->getMockBuilder(Bootstrap::class)->setMethods(['bootstrap'])->getMock();
        $bootstrap
            ->expects($this->once())
            ->method('bootstrap');

        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('get')
            ->with('bootstrap')
            ->willReturn($bootstrap);

        $kernel = new Kernel($services);
        $kernel->add('bootstrap');
        $kernel->run();
    }

    /**
     * Test handle
     */
    public function testHandle()
    {
        $handle = $this->getMockBuilder(Handle::class)->setMethods(['handle'])->getMock();
        $handle
            ->expects($this->once())
            ->method('handle');

        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('get')
            ->with('handle')
            ->willReturn($handle);

        $kernel = new Kernel($services);
        $kernel->add('handle');
        $kernel->run();
    }

    /**
     * Test terminate
     */
    public function testTerminate()
    {
        $terminate = $this->getMockBuilder(Terminate::class)->setMethods(['terminate'])->getMock();
        $terminate
            ->expects($this->once())
            ->method('terminate');

        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('get')
            ->with('terminate')
            ->willReturn($terminate);

        $kernel = new Kernel($services);
        $kernel->add('terminate');
        $kernel->run();
    }

    /**
     * Test fail
     */
    public function testFail()
    {
        $plugin = $this->getMockBuilder(PluginMock::class)->setMethods(['handle', 'fail'])->getMock();
        $plugin
            ->expects($this->at(0))
            ->method('handle')
            ->willThrowException($exception = new RuntimeException('Failed!'));
        $plugin
            ->expects($this->at(1))
            ->method('fail')
            ->with($exception);

        $services = $this->services();
        $services
            ->expects($this->once())
            ->method('get')
            ->with('test')
            ->willReturn($plugin);

        $kernel = new Kernel($services);
        $kernel->add('test');
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
