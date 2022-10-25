<?php

namespace Syringe\Injector;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Syringe\Attribute\{Inject, Qualifier};
use Syringe\Exception\SyringeException;
use Syringe\Repository\{Component, SyringeRepositoryFactory};
use WeakMap;

class ComponentInjector implements SyringeInjector {
    /**
     * @var WeakMap
     */
    protected WeakMap $instances;

    public function __construct() {
        $this->instances = new WeakMap();
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     * @throws SyringeException
     */
    public function spawnClass(string $class): object {
        $reflector = $parent = new ReflectionClass($class);
        $instance = $reflector->newInstanceWithoutConstructor();

        $properties = $reflector->getProperties();
        while ($parent = $parent->getParentClass()) {
            $properties = array_merge($properties, $parent->getProperties());
        }

        foreach ($properties as $property) {
            $injects = $property->getAttributes(Inject::class);
            if (count($injects)) {
                $inject = $injects[0]->newInstance();
                $componentInstance = $this->getComponentInstance($property->getType(), $inject->qualifier);

                $property->setAccessible(true);
                $property->setValue($instance, $componentInstance);
            }
        }

        if ($constructor = $reflector->getConstructor()) {
            $this->invokeMethod($instance, $constructor);
        }

        return $instance;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     * @throws SyringeException
     */
    public function invokeMethod(object $class, ReflectionMethod $method): mixed {
        $parameters = $method->getParameters();
        $paramValues = [];

        foreach ($parameters as $param) {
            $qualifier = $param->getAttributes(Qualifier::class);
            $qualifier = array_shift($qualifier)?->newInstance()->name;

            $paramValues[] = $this->getComponentInstance($param->getType(), $qualifier);
        }

        return $method->invokeArgs($class, $paramValues);
    }

    /**
     * @param string      $class
     * @param string|null $name
     *
     * @return object
     * @throws SyringeException
     * @throws ReflectionException
     */
    protected function getComponentInstance(string $class, ?string $name): object {
        $component = SyringeRepositoryFactory::getInstance()?->getComponent($class, $name);
        if (!$component) {
            throw new SyringeException('No SyringeRepository instance found.');
        }

        if (!empty($this->instances[$component])) {
            return $this->instances[$component];
        }

        $instance = $this->newComponentInstance($component);

        if ($component->isSingleton()) {
            $this->instances[$component] = $instance;
        }

        return $instance;
    }

    /**
     * @param Component $component
     *
     * @return object
     * @throws SyringeException
     * @throws ReflectionException
     */
    protected function newComponentInstance(Component $component): object {
        $reflector = $component->getReflector();
        if ($reflector instanceof ReflectionMethod) {
            $configInstance = $this->spawnClass($reflector->class);
            return $this->invokeMethod($configInstance, $reflector);
        }
        if ($reflector instanceof ReflectionClass) {
            return $this->spawnClass($reflector->getName());
        }
        throw new SyringeException("Invalid \"{$component->getName()}\" component.");
    }
}
