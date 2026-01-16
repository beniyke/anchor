<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Contracts\Service\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceLocatorTrait;

abstract class ServiceLocatorTest extends TestCase
{
    protected function getServiceLocator(array $factories)
    {
        return new class ($factories) implements ContainerInterface {
            use ServiceLocatorTrait;
        };
    }

    public function test_has()
    {
        $locator = $this->getServiceLocator([
            'foo' => function () {
                return 'bar';
            },
            'bar' => function () {
                return 'baz';
            },
            function () {
                return 'dummy';
            },
        ]);

        $this->assertTrue($locator->has('foo'));
        $this->assertTrue($locator->has('bar'));
        $this->assertFalse($locator->has('dummy'));
    }

    public function test_get()
    {
        $locator = $this->getServiceLocator([
            'foo' => function () {
                return 'bar';
            },
            'bar' => function () {
                return 'baz';
            },
        ]);

        $this->assertSame('bar', $locator->get('foo'));
        $this->assertSame('baz', $locator->get('bar'));
    }

    public function test_get_does_not_memoize()
    {
        $i = 0;
        $locator = $this->getServiceLocator([
            'foo' => function () use (&$i) {
                $i++;

                return 'bar';
            },
        ]);

        $this->assertSame('bar', $locator->get('foo'));
        $this->assertSame('bar', $locator->get('foo'));
        $this->assertSame(2, $i);
    }

    public function test_throws_on_undefined_internal_service()
    {
        if (! $this->getExpectedException()) {
            $this->expectException('Psr\Container\NotFoundExceptionInterface');
            $this->expectExceptionMessage('The service "foo" has a dependency on a non-existent service "bar". This locator only knows about the "foo" service.');
        }
        $locator = $this->getServiceLocator([
            'foo' => function () use (&$locator) {
                return $locator->get('bar');
            },
        ]);

        $locator->get('foo');
    }

    public function test_throws_on_circular_reference()
    {
        $this->expectException('Psr\Container\ContainerExceptionInterface');
        $this->expectExceptionMessage('Circular reference detected for service "bar", path: "bar -> baz -> bar".');
        $locator = $this->getServiceLocator([
            'foo' => function () use (&$locator) {
                return $locator->get('bar');
            },
            'bar' => function () use (&$locator) {
                return $locator->get('baz');
            },
            'baz' => function () use (&$locator) {
                return $locator->get('bar');
            },
        ]);

        $locator->get('foo');
    }
}
