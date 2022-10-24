<?php

namespace Syringe\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Provides {
    public function __construct(
        public bool    $primary = false,
        public ?string $name = null, // Alias "qualifier"
        public bool    $singleton = true
    ) { }
}
