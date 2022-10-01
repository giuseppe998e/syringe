<?php

namespace Syringe\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Autowired {
    public function __construct(
        public ?string $qualifier = null // Alias "name"
    ) { }
}
