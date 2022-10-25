<?php

namespace Syringe\Injector;

interface SyringeInjector {
    /**
     * @param string $class
     * @return object
     */
    public function spawnClass(string $class): object;

    /**
     * @param object $class
     * @param \ReflectionMethod $method
     * @return mixed
     */
    public function invokeMethod(object $class, \ReflectionMethod $method): mixed;
}
