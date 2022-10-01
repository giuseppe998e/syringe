<?php

namespace Syringe;

use Syringe\Injector\{BaseInjector, Injector};
use Syringe\Repository\{BeanRepository, BeanRepositoryFactory};
use Syringe\Exception\SyringeException;

class Syringe {
    /**
     * @var Injector|null
     */
    protected static ?Injector $injector = null;

    /**
     * @param string $class
     * @param mixed ...$args
     * @return object
     * @throws \Syringe\Exception\BeanNotFoundException
     * @throws \Syringe\Exception\SyringeException
     * @throws \ReflectionException
     */
    public static function &new(string $class, mixed ...$args): object {
        if (!self::$injector) throw new SyringeException('Syringe has not yet been initialized!');
        return (self::$injector)->spawnClass($class, ...$args);
    }

    /**
     * @param BeanRepository $repository
     * @param Injector|null $injector
     * @return void
     */
    public static function initialize(BeanRepository $repository, ?Injector $injector = null): void {
        BeanRepositoryFactory::register($repository);
        self::$injector = $injector ?? new BaseInjector();
    }
}
