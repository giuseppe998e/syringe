<?php

namespace Syringe\Repository;

class SyringeRepositoryFactory {
    protected static ?SyringeRepository $instance = null;

    public static function register(SyringeRepository $repository): void {
        self::$instance = $repository;
    }

    public static function getInstance(): ?SyringeRepository {
        return self::$instance;
    }
}
