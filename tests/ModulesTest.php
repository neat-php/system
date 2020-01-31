<?php

namespace Neat\System\Test;

use Neat\Service\Container;
use Neat\System\ModuleNotFoundException;
use Neat\System\Services;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Neat\System\Modules;
use ReflectionException;

class ModulesTest extends TestCase
{
    /**
     * @return Container|MockObject
     */
    protected function container(): Container
    {
        return $this->getMockBuilder(Container::class)->getMock();
    }

    /**
     * @return array
     */
    protected function moduleClasses(): array
    {
        return [
            'blog' => Stub\BlogModule::class,
            'user' => Stub\UserModule::class,
        ];
    }

    /**
     * @return array
     */
    protected function modulePaths(): array
    {
        return [
            'blog' => '/path/to/blog',
            'user' => '/path/to/user',
        ];
    }

    /**
     * Test all has and get
     */
    public function testAllHasGet()
    {
        $container = $this->container();
        $container
            ->expects($this->at(0))
            ->method('getOrCreate')
            ->with(Stub\BlogModule::class)
            ->willReturn(new Stub\BlogModule());
        $container
            ->expects($this->at(1))
            ->method('getOrCreate')
            ->with(Stub\UserModule::class)
            ->willReturn(new Stub\UserModule());

        $modules = new Modules($container, $classes = $this->moduleClasses());

        $this->assertTrue($modules->has('blog'));
        $this->assertTrue($modules->has('user'));
        $this->assertFalse($modules->has('unknown'));
        $this->assertSame($classes, $modules->classes());
        $this->assertSame(array_keys($classes), $modules->names());
        $this->assertInstanceOf(Stub\BlogModule::class, $blog = $modules->get('blog'));
        $this->assertInstanceOf(Stub\UserModule::class, $user = $modules->get('user'));
        $this->assertSame(compact('blog', 'user'), $modules->all());
        $this->assertSame(compact('blog'), $modules->implementing(Services::class));
        $this->assertSame($classes['blog'], $modules->class('blog'));
        $this->assertSame($classes['user'], $modules->class('user'));
        $this->assertSame(__DIR__ . '/Stub', $modules->path('user'));
        $this->assertEquals(['blog' => __DIR__ . '/Stub', 'user' => __DIR__ . '/Stub'], $modules->paths());
    }

    /**
     * Test paths
     *
     * @throws ReflectionException
     */
    public function testCustomPaths()
    {
        $modules = new Modules($this->container(), $this->moduleClasses(), $paths = $this->modulePaths());

        $this->assertEquals($paths, $modules->paths());
        $this->assertEquals($paths['blog'], $modules->path('blog'));
        $this->assertEquals($paths['user'], $modules->path('user'));
    }

    /**
     * Test map using a callable with an interface
     */
    public function testMapAll()
    {
        $blog = $this->getMockBuilder(Stub\BlogModule::class)->getMock();
        $blog
            ->expects($this->once())
            ->method('stuff');

        $user = $this->getMockBuilder(Stub\UserModule::class)->getMock();
        $user
            ->expects($this->once())
            ->method('stuff');

        $container = $this->container();
        $container
            ->expects($this->at(0))
            ->method('getOrCreate')
            ->with(Stub\BlogModule::class)
            ->willReturn($blog);
        $container
            ->expects($this->at(1))
            ->method('getOrCreate')
            ->with(Stub\UserModule::class)
            ->willReturn($user);

        $modules = new Modules($container, $this->moduleClasses());
        $modules->map(function ($module) use ($container) {
            /** @noinspection PhpUndefinedMethodInspection */
            $module->stuff();
        });
    }

    /**
     * Test map using a callable with an interface
     */
    public function testMapInterface()
    {
        $blog = $this->getMockBuilder(Stub\BlogModule::class)->getMock();
        $blog
            ->expects($this->once())
            ->method('services');

        $user = $this->getMockBuilder(Stub\UserModule::class)->getMock();

        $container = $this->container();
        $container
            ->expects($this->at(0))
            ->method('getOrCreate')
            ->with(Stub\BlogModule::class)
            ->willReturn($blog);
        $container
            ->expects($this->at(1))
            ->method('getOrCreate')
            ->with(Stub\UserModule::class)
            ->willReturn($user);

        $modules = new Modules($container, $this->moduleClasses());
        $modules->map(function (Services $module) use ($container) {
            $module->services($container);
        });
    }

    public function testGetThrowsNotFoundException()
    {
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("Module not found: unknown");

        $modules = new Modules($this->container());
        $modules->get('unknown');
    }

    public function testClassThrowsNotFoundException()
    {
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("Module not found: unknown");

        $modules = new Modules($this->container());
        $modules->class('unknown');
    }

    public function testPathThrowsNotFoundException()
    {
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage("Module not found: unknown");

        $modules = new Modules($this->container());
        $modules->path('unknown');
    }
}
