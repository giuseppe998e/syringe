<?php

namespace Syringe\Repository;

interface SyringeRepository {
    /**
     * @param string $class
     * @param string|null $name
     * @return Component
     */
    public function getComponent(string $class, ?string $name): Component;
}
