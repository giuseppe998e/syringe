<?php

namespace Syringe\Mockups;

use Syringe\Attribute\Provides;
use Syringe\Attribute\Qualifier;

class MockupConfiguration {
    #[Provides(primary: true)]
    public function getSingletonRandomGen(): RandomGen {
        return new RandomGen();
    }

    #[Provides(singleton: false)]
    public function getOneTimeRandomGen(#[Qualifier("getSingletonRandomGen")] RandomGen $max): RandomGen {
        return new RandomGen(max: $max->getRand());
    }
}
