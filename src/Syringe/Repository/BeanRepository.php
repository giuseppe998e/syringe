<?php

namespace Syringe\Repository;

use Syringe\Attribute\Bean;

interface BeanRepository {
    /**
     * @param string $class
     * @param string|null $name
     * @return Bean
     */
    public function &getBean(string $class, ?string $name): Bean;
}
