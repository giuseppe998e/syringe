<?php

namespace Syringe\Repository;

use ReflectionClass;
use ReflectionMethod;
use Reflector;
use Syringe\Attribute\Provides;
use Syringe\Exception\SyringeException;

class Component {
    /**
     * @param bool $primary
     * @param string|null $name
     * @param bool $singleton
     * @param Reflector $reflector
     */
    public function __construct(
        protected bool      $primary,
        protected ?string   $name,
        protected bool      $singleton,
        protected Reflector $reflector
    ) { }

    /**
     * @return bool
     */
    public function isPrimary(): bool {
        return $this->primary;
    }

    /**
     * @return string
     * @throws SyringeException
     */
    public function getName(): string {
        if (!$this->name) {
            if ($this->reflector instanceof ReflectionMethod || $this->reflector instanceof ReflectionClass) {
                $this->name = $this->reflector->getName();
            } else {
                throw new SyringeException('Malformed component.');
            }
        }
        return $this->name;
    }

    /**
     * @return string
     * @throws SyringeException
     */
    public function getType(): string {
        if ($this->reflector instanceof ReflectionMethod) {
            return (string)$this->reflector->getReturnType();
        }
        if ($this->reflector instanceof ReflectionClass) {
            return $this->reflector->getName();
        }
        throw new SyringeException('Malformed component.');
    }

    /**
     * @return bool
     */
    public function isSingleton(): bool {
        return $this->singleton;
    }

    /**
     * @return Reflector
     */
    public function getReflector(): Reflector {
        return $this->reflector;
    }

    /**
     * @param Provides $provides
     * @param Reflector $reflection
     * @return Component
     */
    public static function fromProvidesAttribute(Provides $provides, Reflector $reflection): self {
        return new self($provides->primary, $provides->name, $provides->singleton, $reflection);
    }
}
