<?php

namespace Syringe\Repository;

use Syringe\Exception\ComponentNotFoundException;

class ComponentBucket {
    /**
     * @var Component[]
     */
    protected array $components;

    /**
     * @var Component|null
     */
    protected ?Component $first, $primary;

    public function __construct() {
        $this->components = [];
        $this->first = $this->primary = null;
    }

    /**
     * @param string $name
     * @param Component $component
     */
    public function addComponent(string $name, Component $component): void {
        if (!count($this->components)) {
            $this->first = $component;
        }
        if (!$this->primary && $component->isPrimary()) {
            $this->primary = $component;
        }
        $this->components[$name] = $component;
    }

    /**
     * @param string|null $name
     * @return Component
     * @throws ComponentNotFoundException
     */
    public function getComponent(?string $name): Component {
        if (1 === count($this->components)) {
            return $this->first;
        }
        if (!$name) {
            if ($this->primary) {
                return $this->primary;
            }
            $qualifiers = implode(', ', array_keys($this->components));
            throw new ComponentNotFoundException("No primary Provides has been set, must choose a qualifier from: $qualifiers");
        }
        if ($component = $this->components[$name]) {
            return $component;
        }
        throw new ComponentNotFoundException("There is no Provides with name \"$name\".");
    }
}
