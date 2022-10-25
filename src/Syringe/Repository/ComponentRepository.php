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
     * @param string[] $classes
     * @throws \ReflectionException
     * @throws \Syringe\Exception\SyringeException
     */
    public function addConfigurations(string ...$classes): void {
        foreach ($classes as $config) {
            $this->addConfiguration($config);
        }
    }

    /**
     * @param string $class
     * @throws \ReflectionException
     * @throws \Syringe\Exception\SyringeException
     */
    public function addConfiguration(string $class): void {
        $reflector = new \ReflectionClass($class);
        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            $provides = $method->getAttributes(Provides::class);
            if (count($provides)) {
                $provide = $provides[0]->newInstance();
                $component = Component::fromProvidesAttribute($provide, $method);
                $type = $component->getType();

                $bucket = $this->buckets[$type] ??= new ComponentBucket();
                $bucket->addComponent($component);
            }
        }
    }

    /**
     * @inheritDoc
     * @throws ComponentNotFoundException
     */
    public function getComponent(string $class, ?string $name): Component {
        if (array_key_exists($class, $this->buckets)) {
            return $this->buckets[$class]->getComponent($name);
        }
        throw new ComponentNotFoundException("There is no Provides for the class \"$class\".");
    }
}
