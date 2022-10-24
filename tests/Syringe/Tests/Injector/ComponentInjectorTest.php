<?php declare(strict_types = 1);

namespace Syringe\Tests\Injector;

use PHPUnit\Framework\TestCase;
use Syringe\Attribute\Qualifier;
use Syringe\Injector\ComponentInjector;
use Syringe\Mockups\MockupComponent;
use Syringe\Mockups\MockupConfiguration;
use Syringe\Mockups\RandomGen;
use Syringe\Repository\ComponentRepository;
use Syringe\Repository\SyringeRepository;
use Syringe\Repository\SyringeRepositoryFactory;

class ComponentInjectorTest extends TestCase {
    public function setUp(): void {
        $repo = new ComponentRepository();
        $repo->addConfiguration(MockupConfiguration::class);
        SyringeRepositoryFactory::register($repo);
    }

    public function testSpawnClass(): void {
        $injector = new ComponentInjector();

        // Not existent component
        $this->expectException(\ReflectionException::class);
        $injector->spawnClass("NonExistent\\Module\\UnknownComponent");

        // Mockup component
        $mockupComponent1 = $injector->spawnClass(MockupComponent::class);
        $mockupComponent2 = $injector->spawnClass(MockupComponent::class);

        // Test singleton injected component
        $singletonRand1 = $mockupComponent1->getSingletonRand();
        $singletonRand2 = $mockupComponent2->getSingletonRand();
        $this->assertEquals($singletonRand1, $singletonRand2);

        // Test one-time injected component
        $oneTimeRand1 = $mockupComponent1->getOneTimeRand();
        $oneTimeRand2 = $mockupComponent2->getOneTimeRand();
        $this->assertNotEquals($oneTimeRand1, $oneTimeRand2);
    }

    public function testInvokeMethod(): void {
        $injector = new ComponentInjector();

        $mockupPrimarySingletonRandFn = new \ReflectionMethod($this, "mockupPrimarySingletonRandFn");
        $mockupSingletonRandFn = new \ReflectionMethod($this, "mockupSingletonRandFn");
        $mockupOneTimeRandFn = new \ReflectionMethod($this, "mockupOneTimeRandFn");

        // Test singleton invoked methods
        $primarySingletonRand = $injector->invokeMethod($this, $mockupPrimarySingletonRandFn);
        $singletonRand = $injector->invokeMethod($this, $mockupSingletonRandFn);
        $this->assertEquals($primarySingletonRand, $singletonRand);

        // Test one-time invoked methods
        $oneTimeRand1 = $injector->invokeMethod($this, $mockupOneTimeRandFn);
        $oneTimeRand2 = $injector->invokeMethod($this, $mockupOneTimeRandFn);
        $this->assertNotEquals($oneTimeRand1, $oneTimeRand2);
        $this->assertNotEquals($oneTimeRand1, $primarySingletonRand);
    }

    /* Mockup functions */
    public function mockupPrimarySingletonRandFn(RandomGen $primarySingletonRand): int {
        return $primarySingletonRand->getRand();
    }

    public function mockupSingletonRandFn(#[Qualifier("getSingletonRandomGen")] RandomGen $singletonRand): int {
        return $singletonRand->getRand();
    }

    public function mockupOneTimeRandFn(#[Qualifier("getOneTimeRandomGen")] RandomGen $oneTimeRand): int {
        return $oneTimeRand->getRand();
    }
}
