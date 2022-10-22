<?php

namespace Syringe\Injector;

use Syringe\Attribute\{Inject, Qualifier};
use Syringe\Exception\SyringeException;
use Syringe\Repository\SyringeRepositoryFactory;

class ComponentInjector implements SyringeInjector {
    /**
     * @var \WeakMap
     */
    protected \WeakMap $instances;

    public function __construct() {
        $this->instances = new \WeakMap();
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     * @throws SyringeException
     */
    public function spawnClass(string $class): object {
        $classReflect = $parent = new \ReflectionClass($class);
        $classInstance = $classReflect->newInstanceWithoutConstructor();

        $properties = $classReflect->getProperties();
        while ($parent = $parent->getParentClass()) {
            $properties = array_merge($properties, $parent->getProperties());
        }

        foreach ($properties as $property) {
            if ($autowired = $property->getAttributes(Inject::class)[0]) {
                $autowired = $autowired->newInstance();
                $componentInstance = $this->getComponentInstance($property->getType(), $autowired->qualifier);
                $property->setAccessible(true);
                $property->setValue($classInstance, $componentInstance);
            }
        }

        if ($constructor = $classReflect->getConstructor()) {
            $this->invokeMethod($classInstance, $constructor);
        }

        return $classInstance;
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     * @throws SyringeException
     */
    public function invokeMethod(object $classInstance, \ReflectionMethod $method): mixed {
        $parameters = $method->getParameters();
        $paramValues = [];
        foreach ($parameters as $param) {
            $qualifier = $param->getAttributes(Qualifier::class);
            $qualifier = $qualifier[0]?->newInstance()->name;
            $paramValues[] = $this->getComponentInstance($param->getType(), $qualifier);
        }
        return $method->invokeArgs($classInstance, $paramValues);
    }

    /**
     * @param string $class
     * @param string|null $name
     * @return object
     * @throws SyringeException
     * @throws \ReflectionException
     */
    protected function getComponentInstance(string $class, ?string $name): object {
        $component = SyringeRepositoryFactory::getInstance()?->getComponent($class, $name);
        if (!$component) {
            throw new SyringeException('No SyringeRepository instance found.');
        }
        if (isset($this->instances[$component])) {
            return $this->instances[$component];
        }

        $reflection = $component->getReflector();
        if ($reflection instanceof \ReflectionMethod) {
            $configInstance = $this->spawnClass($reflection->class);
            $componentInstance = $this->invokeMethod($configInstance, $reflection);
        } elseif ($reflection instanceof \ReflectionClass) {
            $componentInstance = $this->spawnClass($reflection->getName());
        } else {
            throw new SyringeException("Invalid \"$class" . ($name ? "::$name" : '') . "\" component.");
        }

        if ($component->isSingleton()) {
            $this->instances[$component] = $componentInstance;
        }

        return $componentInstance;
    }
}
