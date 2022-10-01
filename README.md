# Syringe
Dependency Injection Framework for PHP8.

## Usage
#### The bean class:
```php
<?php

use Syringe\Attribute\Autowired;

class RandomNumComponent {
    private int $rand;

    public function __construct() {
        $this->rand = rand();
    }

    public function getValue(): int {
        return $this->rand;
    }
}
```

#### The configuration class:
```php
<?php

use Syringe\Attribute\{Bean, Qualifier};

class Configuration {
    // bool "primary" - Sets the Bean as primary if more than one of the same type is available.
    // ?string "name" - Custom name (or qualifier) for the Bean. (Set "null" to use the method name)
    // bool "singleton" - Denotes that the Bean is a singleton
    #[Bean(primary: false, name: null, singleton: true)] // Default values
    public function getRandomNumComponent(
        //#[Qualifier("getCustomNameComp2")] CustomNameComponent $comp // Autowired parameter component
    ): RandomNumComponent {
        // ...
        return new RandomNumComponent();
    }

    // ...
}
```

#### The component class:
```php
<?php

use Syringe\Attribute\Autowired;

class Component {
    #[Autowired("getRandomNumComponent")]
    // or #[Autowired(qualifier: "getRandomNumComponent")]
    private RandomNumComponent $randomNum;

    public function getClassAndRandom(): string {
        return self::class . "#{$randomNum->getValue()}";
    }
}

```

#### Initialize Syringe:
```php
<?php

use Syringe\Syringe;
use Syringe\Repository\BaseBeanRepository;

$beanRepo = new BaseBeanRepository();
$beanRepo->addConfiguration(Configuration::class);
Syringe::initialize($beanRepo);

$component = Syringe::new(Component::class);
echo $component->getClassAndRandom();
```
