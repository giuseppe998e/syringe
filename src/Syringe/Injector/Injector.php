<?php

namespace Syringe\Injector;

interface Injector {
    /**
     * @param string $class
     * @return object
     */
    public function &spawnClass(string $class): object;

    /**
     * @param \ReflectionMethod $method
     * @param object $classInstance
     * @return mixed
     */
    public function invokeMethod(object $classInstance, \ReflectionMethod $method): mixed;
}
