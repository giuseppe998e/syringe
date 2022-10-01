<?php

namespace Syringe\Injector;

use Syringe\Attribute\Autowired;
use Syringe\Repository\BeanRepositoryFactory;

class BaseInjector implements Injector {
    /**
     * @var object[]
     */
    protected array $beanInstances;

    public function __construct() {
        $this->beanInstances = [];
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function &spawnClass(string $class, mixed ...$args): object {
        $classReflect = $parent = new \ReflectionClass($class);

        $properties = $classReflect->getProperties();
        while ($parent = $parent->getParentClass())
            $properties = array_merge($properties, $parent->getProperties());

        $classInstance = $classReflect->newInstanceWithoutConstructor();

        foreach ($properties as $property) {
            $autowired = $property->getAttributes(Autowired::class)[0];
            if (!$autowired) continue;

            $autowired = $autowired->newInstance();
            $beanInstance = $this->getBeanInstance($property->getType(), $autowired->qualifier);

            $property->setAccessible(true);
            $property->setValue($classInstance, $beanInstance);
        }

        $constructor = $classReflect->getConstructor();
        if ($constructor) $this->invokeMethod($classInstance, $constructor);
        return $classInstance;
    }

    /**
     * @inheritDoc
     * @throws \ReflectionException
     */
    public function invokeMethod(object $classInstance, \ReflectionMethod $method): mixed {
        $parameters = $method->getParameters();
        $paramValues = [];

        foreach ($parameters as $param)
            $paramValues[] = $this->getBeanInstance($param->getType(), null); // TODO qualifier attribute

        return $method->invokeArgs($classInstance, $paramValues);
    }

    /**
     * @param string $class
     * @param string|null $name
     * @throws \ReflectionException
     * @return object
     */
    protected function &getBeanInstance(string $class, ?string $name): object {
        $bean = BeanRepositoryFactory::getInstance()->getBean($class, $name);
        $hashCode = $bean->hashCode();

        if ($this->beanInstances[$hashCode])
            return $this->beanInstances[$hashCode];

        $classInstance = $this->spawnClass($bean->method->class);
        $beanInstance = $this->invokeMethod($classInstance, $bean->method);

        if ($bean->singleton)
            $this->beanInstances[$hashCode] = &$beanInstance;
        return $beanInstance;
    }
}
