<?php

namespace Syringe\Mockups;

use Syringe\Attribute\Inject;

class MockupComponent {
    #[Inject] // Gets primary
    private RandomGen $singletonRand;

    #[Inject("getOneTimeRandomGen")]
    private RandomGen $oneTimeRand;

    public function getSingletonRand(): int {
        return $this->singletonRand->getRand();
    }

    public function getOneTimeRand(): int {
        return $this->oneTimeRand->getRand();
    }
}
