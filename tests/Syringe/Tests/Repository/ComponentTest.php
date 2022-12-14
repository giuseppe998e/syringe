<?php declare(strict_types=1);

namespace Syringe\Tests\Repository;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Syringe\Repository\Component;

class ComponentTest extends TestCase {
    protected ?ReflectionClass $reflectionClass = null;
    protected ?ReflectionMethod $reflectionMethod = null;

    public function setUp(): void {
        if (!($this->reflectionClass && $this->reflectionMethod)) {
            $this->reflectionClass = new ReflectionClass($this);
            $this->reflectionMethod = new ReflectionMethod($this, "reflectionTestMethod");
        }
    }

    public function testReflectionClassGetName(): void {
        $class = new Component(false, null, true, $this->reflectionClass);
        $this->assertEquals(self::class, $class->getName());
    }

    public function testReflectionClassGetType(): void {
        $class = new Component(false, null, true, $this->reflectionClass);
        $this->assertEquals(self::class, $class->getType());
        $this->assertEquals($class->getName(), $class->getType());
    }

    public function testReflectionClassGetReflector(): void {
        $class = new Component(false, null, true, $this->reflectionClass);
        $this->assertEquals($this->reflectionClass, $class->getReflector());
    }

    public function testReflectionMethodGetName(): void {
        $class = new Component(false, null, true, $this->reflectionMethod);
        $this->assertEquals("reflectionTestMethod", $class->getName());
    }

    public function testReflectionMethodGetType(): void {
        $class = new Component(false, null, true, $this->reflectionMethod);
        $this->assertEquals(self::class, $class->getType());
    }

    public function testReflectionMethodGetReflector(): void {
        $class = new Component(false, null, true, $this->reflectionMethod);
        $this->assertEquals($this->reflectionMethod, $class->getReflector());
    }

    // Mockup method(s)

    private function reflectionTestMethod(): ComponentTest {
        throw new RuntimeException("Not Implemented!");
    }
}
