<?php

namespace Syringe\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Qualifier {
    public function __construct(
        public string $name // Alias "qualifier"
    ) { }
}
