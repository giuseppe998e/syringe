<?php

namespace Syringe\Repository;

use Syringe\Attribute\Bean;
use Syringe\Exception\BeanNotFoundException;

class BeanBucket {
    /**
     * @var Bean[]
     */
    protected array $beans;

    /**
     * @var Bean|null
     */
    protected ?Bean $first, $primary;

    public function __construct() {
        $this->beans = [];
        $this->first = $this->primary = null;
    }

    /**
     * @param string $name
     * @param Bean $bean
     */
    public function addBean(string $name, Bean $bean): void {
        if (!count($this->beans)) $this->first = &$bean;
        if ($bean->primary && !$this->primary) $this->primary = &$bean;
        $this->beans[$name] = &$bean;
    }

    /**
     * @param string|null $name
     * @return Bean
     * @throws BeanNotFoundException
     */
    public function &getBean(?string $name): Bean {
        if (1 === count($this->beans)) return $this->first;
        if (!$name) {
            if ($this->primary) return $this->primary;
            $qualifiers = implode(', ', array_keys($this->beans));
            throw new BeanNotFoundException("No primary Bean has been set, must choose a qualifier from: $qualifiers");
        }
        $bean = &$this->beans[$name];
        if ($bean) return $bean;
        throw new BeanNotFoundException("There is no Bean with name \"$name\".");
    }
}
