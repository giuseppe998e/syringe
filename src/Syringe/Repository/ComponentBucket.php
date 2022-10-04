<?php

namespace Syringe\Repository;

use Syringe\Attribute\Provides;
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
        $this->components[$name] = $component;
        if (1 === count($this->components))
            $this->first = &$this->components[$name];
        if ($component->isPrimary() && !$this->primary)
            $this->primary = &$this->components[$name];
    }

    /**
     * @param string|null $name
     * @return Component
     * @throws ComponentNotFoundException
     */
    public function getComponent(?string $name): Component {
        if (1 === count($this->components)) return $this->first;
        if (!$name) {
            if ($this->primary) return $this->primary;
            $qualifiers = implode(', ', array_keys($this->components));
            throw new ComponentNotFoundException("No primary Provides has been set, must choose a qualifier from: $qualifiers");
        }
        $component = $this->components[$name];
        if ($component) return $component;
        throw new ComponentNotFoundException("There is no Provides with name \"$name\".");
    }
}
