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
     * @param string $configuration
     * @throws \ReflectionException
     */
    public function addConfiguration(string $configuration): void {
        $reflect = new \ReflectionClass($configuration);
        $methods = $reflect->getMethods();
        foreach ($methods as $method) {
            $bean = $method->getAttributes(Bean::class)[0];
            if (!$bean) continue;
            $bean = $bean->newInstance();
            $bean->method = $method;
            $class = strval($method->getReturnType());
            $name = $bean->name ?? $method->getName();
            $bucket = $this->beanBuckets[$class] ?? ($this->beanBuckets[$class] = new BeanBucket());
            $bucket->addBean($name, $bean);
        }
    }

    /**
     * @inheritDoc
     * @throws BeanNotFoundException
     */
    public function getBean(string $class, ?string $name): Bean {
        $bucket = $this->beanBuckets[$class];
        if ($bucket) return $bucket->getBean($name);
        throw new BeanNotFoundException("There is no Bean for the class \"$class\".");
    }
}
