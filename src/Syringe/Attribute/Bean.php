<?php

namespace Syringe\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Bean {
    public ?\ReflectionMethod $method;

    public function __construct(
        public bool $primary = false,
        public ?string $name = null, // Alias "qualifier"
        public bool $singleton = true) {
        $this->method = null;
    }

    public function hashCode(): int {
        $hash = 12289;
        if ($this->method) {
            $hash = (($hash << 5) - $hash) + crc32($this->method->class);
            $hash = (($hash << 5) - $hash) + crc32($this->method->name);
        }
        if ($this->name) $hash = (($hash << 5) - $hash) + crc32($this->name);
        $hash = (($hash << 5) - $hash) + crc32($this->primary);
        return (($hash << 5) - $hash) + crc32($this->singleton);
    }
}
