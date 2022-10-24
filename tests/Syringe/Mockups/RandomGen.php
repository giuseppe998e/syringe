<?php

namespace Syringe\Mockups;

class RandomGen {
    private int $rand;

    public function __construct(int $min = PHP_INT_MIN, int $max = PHP_INT_MAX) {
        $this->rand = random_int($min, $max);
    }

    public function getRand(): int {
        return $this->rand;
    }
}
