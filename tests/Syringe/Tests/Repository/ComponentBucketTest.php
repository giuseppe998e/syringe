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

        self::$mockComponent = new Component(false, "AnonymousClass#1", false, $reflection);
        self::$mockPrimaryComponent = new Component(true, "AnonymousClass#Primary", false, $reflection);
    }

    public function testGetComponent(): void {
        $bucket = new ComponentBucket();

        // Get first/unique component
        $bucket->addComponent(self::$mockComponent);
        $this->assertEquals(self::$mockComponent, $bucket->getComponent(null));

        // Get a no-name component W/O a given primary
        $bucket->addComponent(self::$mockComponent);
        $this->expectException(ComponentNotFoundException::class);
        $this->assertEquals(self::$mockComponent, $bucket->getComponent(null));

        // Get a no-name component WITH a given primary
        $bucket->addComponent(self::$mockPrimaryComponent);
        $this->assertEquals(self::$mockPrimaryComponent, $bucket->getComponent(null));

        // Get a named component
        $this->assertEquals(self::$mockComponent, $bucket->getComponent("AnonymousClass#1"));

        // Get an unknown component
        $this->expectException(ComponentNotFoundException::class);
        $bucket->getComponent("AnonymousClass#unknown");
    }
}
