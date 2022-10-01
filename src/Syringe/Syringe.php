<?php

namespace Syringe;

use Syringe\Injector\{BaseInjector, Injector};
use Syringe\Repository\{BeanRepository, BeanRepositoryFactory};

class Syringe {
    /**
     * @var Injector|null
     */
    protected static ?Injector $injector = null;

    /**
     * @param string $class
     * @param mixed ...$args
     * @throws \ReflectionException
     * @throws \Syringe\Exception\BeanNotFoundException
     * @return object
     */
    public static function &new(string $class, mixed ...$args): object {
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
