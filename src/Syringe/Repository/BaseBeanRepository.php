<?php

namespace Syringe\Repository;

use Syringe\Attribute\Bean;
use Syringe\Exception\BeanNotFoundException;

class BaseBeanRepository implements BeanRepository {
    /**
     * @var BeanBucket[]
     */
    protected array $beanBuckets;

    public function __construct() {
        $this->beanBuckets = [];
    }

    /**
     * @param string $class
     * @throws \ReflectionException
     */
    public function addConfiguration(string $class): void {
        $reflect = new \ReflectionClass($class);
        $methods = $reflect->getMethods();

        foreach ($methods as $method) {
            $bean = $method->getAttributes(Bean::class)[0];
            if (!$bean) continue;

            $bean = $bean->newInstance();
            $bean->method = $method;

            $type = strval($method->getReturnType());
            $qualifier = $bean->name ?? $method->getName();
            $bucket = $this->beanBuckets[$type] ?? ($this->beanBuckets[$type] = new BeanBucket());
            $bucket->addBean($qualifier, $bean);
        }
    }

    /**
     * @inheritDoc
     * @throws BeanNotFoundException
     */
    public function &getBean(string $class, ?string $name): Bean {
        $bucket = &$this->beanBuckets[$class];
        if ($bucket) return $bucket->getBean($name);
        throw new BeanNotFoundException("There is no Bean for the class \"$class\".");
    }
}
