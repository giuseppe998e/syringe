<?php

namespace Syringe;

use Syringe\Exception\SyringeException;
use Syringe\Injector\{ComponentInjector, SyringeInjector};
use Syringe\Repository\{SyringeRepository, SyringeRepositoryFactory};

class Syringe {
    /**
     * @var SyringeInjector|null
     */
    protected static ?SyringeInjector $injector = null;

    /**
     * @param string $class
     * @return object
     * @throws \Syringe\Exception\ComponentNotFoundException
     * @throws SyringeException
     * @throws \ReflectionException
     */
    public static function new(string $class): object {
        if (!self::$injector) {
            throw new SyringeException('Syringe has not yet been initialized!');
        }
        return (self::$injector)->spawnClass($class);
    }

    /**
     * @param SyringeRepository $repository
     * @param SyringeInjector|null $injector
     * @return void
     */
    public static function initialize(SyringeRepository $repository, ?SyringeInjector $injector = null): void {
        SyringeRepositoryFactory::register($repository);
        self::$injector = $injector ?? new ComponentInjector();
    }
}
