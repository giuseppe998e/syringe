<?php

namespace Syringe\Repository;

class BeanRepositoryFactory {
    protected static ?BeanRepository $instance = null;

    public static function register(BeanRepository $repository): void {
        self::$instance = $repository;
    }

    public static function getInstance(): ?BeanRepository {
        return self::$instance;
    }
}
