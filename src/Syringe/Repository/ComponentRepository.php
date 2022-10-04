<?php

namespace Syringe\Repository;

use Syringe\Attribute\Provides;
use Syringe\Exception\ComponentNotFoundException;

class ComponentRepository implements SyringeRepository {
    /**
     * @var ComponentBucket[]
     */
    protected array $buckets;

    public function __construct() {
        $this->buckets = [];
    }

    /**
     * @param string[] $configurations
     * @throws \ReflectionException
     * @throws \Syringe\Exception\SyringeException
     */
    public function addConfigurations(string ...$configurations): void {
        foreach ($configurations as $config) $this->addConfiguration($config);
    }

    /**
     * @param string $configuration
     * @throws \ReflectionException
     * @throws \Syringe\Exception\SyringeException
     */
    public function addConfiguration(string $configuration): void {
        $reflect = new \ReflectionClass($configuration);
        $methods = $reflect->getMethods();
        foreach ($methods as $method) {
            $provides = $method->getAttributes(Provides::class)[0];
            if (!$provides) continue;

            $providesInstance = $provides->newInstance();
            $component = Component::fromProvidesAttribute($providesInstance, $method);
            $class = $component->getType();
            $name = $component->getName();

            $bucket = $this->buckets[$class] ??= new ComponentBucket();
            $bucket->addComponent($name, $component);
        }
    }

    /**
     * @inheritDoc
     * @throws ComponentNotFoundException
     */
    public function getComponent(string $class, ?string $name): Component {
        $bucket = $this->buckets[$class];
        if ($bucket) return $bucket->getComponent($name);
        throw new ComponentNotFoundException("There is no Provides for the class \"$class\".");
    }
}
