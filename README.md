# Syringe
Dependency Injection Framework for PHP8.

## Usage
#### The configuration class:

```php
<?php

use PDO;
use Syringe\Attribute\{Provides, Qualifier};

class DbConfiguration {
    // bool "primary" - Sets the Provides as primary if more than one of the same type is available.
    // ?string "name" - Custom name (or qualifier) for the Provides. (Set "null" to use the method name)
    // bool "singleton" - Denotes that the Provides is a singleton
    #[Provides(primary: false, name: null, singleton: true)] // Default values
    public function getMariaDBConnection(): PDO {
        $dsn = 'mysql:dbname=mariadb;host=127.0.0.1;port=3307';
        $user = 'root';
        $password = 'password';
        return new PDO($dsn, $user, $password);
    }

    #[Provides(primary: true)]
    public function getMySQLConnection(): PDO {
        $dsn = 'mysql:dbname=mysqldb;host=127.0.0.1;port=3306';
        $user = 'root';
        $password = 'password';
        return new PDO($dsn, $user, $password);
    }

/*
    public function getCarsRepository(
        // Without "Qualifier" the parameter will be bound to
        // "getMySQLConnection" (because it's primary for the PDO class)
        #[Qualifier("getMariaDBConnection")] PDO $db
    ): CarsRepository {
        return new CarsRepository($db);
    }
*/

    // ...
}
```


#### The component class:

```php
<?php

use Syringe\Attribute\Inject;

class TestClass {
    #[Inject] // Injects "getMySQLConnection" because it's set as primary
    // #[Inject("getMariaDBConnection")]
    // or #[Inject(qualifier: "getMySQLConnection")]
    private PDO $db;

    public function getUserById(int $id): array {
         $stmt = $db->prepare('SELECT * FROM users WHERE id=?');
         $stmt->execute([$id]);
         return $stmt->fetch();
    }
}

```


#### Initialize Syringe:

```php
<?php

use Syringe\Syringe;
use Syringe\Repository\ComponentRepository;

$beanRepo = new ComponentRepository();
$beanRepo->addConfiguration(DbConfiguration::class);
// $beanRepo->addConfiguration(OtherConfiguration::class);
Syringe::initialize($beanRepo);

$testClass = Syringe::new(TestClass::class);
$user = $testClass->getUserById(1);
var_dump($user);

// ...
```
