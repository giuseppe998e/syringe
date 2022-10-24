<?php declare(strict_types = 1);

namespace Syringe\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Syringe\Exception\ComponentNotFoundException;
use Syringe\Repository\Component;
use Syringe\Repository\ComponentBucket;

class ComponentBucketTest extends TestCase {
    protected static Component $mockComponent;
    protected static Component $mockPrimaryComponent;

    public static function setUpBeforeClass(): void {
        $reflection = new \ReflectionClass(new class { });

        self::$mockComponent = new Component(false, "AnonymousClass", false, $reflection);
        self::$mockPrimaryComponent = new Component(true, "AnonymousClass", false, $reflection);
    }

    public function testGetComponent(): void {
        $bucket = new ComponentBucket();

        // Get first/unique component
        $bucket->addComponent("AnonymousComp#1", self::$mockComponent);
        $this->assertEquals(self::$mockComponent, $bucket->getComponent(null));

        // Get a no-name component W/O a given primary
        $bucket->addComponent("AnonymousComp#2", self::$mockComponent);
        $this->expectException(ComponentNotFoundException::class);
        $this->assertEquals(self::$mockComponent, $bucket->getComponent(null));

        // Get a no-name component WITH a given primary
        $bucket->addComponent("AnonymousComp#3", self::$mockPrimaryComponent);
        $this->assertEquals(self::$mockPrimaryComponent, $bucket->getComponent(null));

        // Get a named component
        $this->assertEquals(self::$mockComponent, $bucket->getComponent("AnonymousComp#2"));

        // Get an unknown component
        $this->expectException(ComponentNotFoundException::class);
        $this->assertEquals(self::$mockComponent, $bucket->getComponent("AnonymousComp#unknown"));
    }
}
