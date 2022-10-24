<?php declare(strict_types = 1);

namespace Syringe\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Syringe\Exception\ComponentNotFoundException;
use Syringe\Mockups\MockupConfiguration;
use Syringe\Mockups\RandomGen;
use Syringe\Repository\ComponentRepository;

class ComponentRepositoryTest extends TestCase {
    public function testAddConfiguration(): void {
        $repository = new ComponentRepository();

        $this->expectException(\ReflectionException::class);
        $repository->addConfiguration("NonExistent\\Module\\UnknownConfiguration");

        $repository->addConfiguration(MockupConfiguration::class);
        $this->assertTrue(true);
    }

    public function testGetComponent(): void {
        $repository = new ComponentRepository();
        $repository->addConfiguration(MockupConfiguration::class);

        $this->expectException(ComponentNotFoundException::class);
        $repository->getComponent(self::class, null);

        // Tests primary && NON singleton
        $oneTimeRandom = $repository->getComponent(RandomGen::class, "getOneTimeRandomGen");
        $this->assertTrue($oneTimeRandom->isPrimary() && !$oneTimeRandom->isSingleton());

        // Tests NOT primary && singleton
        $singletonRandom = $repository->getComponent(RandomGen::class, null);
        $this->assertTrue(!$singletonRandom->isPrimary() && $singletonRandom->isSingleton());
    }
}
