<?php

namespace GenericEntity;

use GenericEntity\Spec\Native\StringSpec;
use GenericEntity\Spec\Native\BooleanSpec;
use GenericEntity\Spec\ObjectSpec;


class Factory
{
    protected $_specs = [];

    public function __construct()
    {
        // Add native specs
        $this->addSpec('string',  new StringSpec());
        $this->addSpec('boolean', new BooleanSpec());
    }

    public function createSpec(string $name, array $specMetadata, bool $isExtensible)
    {
        $spec = new ObjectSpec($specMetadata, $isExtensible);

        $this->addSpec($name, $spec);

        return $this;
    }

    public function addSpec($name, $spec)
    {
        if (array_key_exists($name, $this->_specs)) {
            throw new \GenericEntity\DuplicatedSpecException("Spec '$name' already defined.");
        }

        $this->_specs[$name] = $spec;

        return $this;
    }

    public function hasSpec($name)
    {
        return array_key_exists($name, $this->_specs);
    }

    public function getSpec($name)
    {
        if (!$this->hasSpec($name)) {
            throw new \RuntimeException("Spec '$name' does not exist.");
        }

        return $this->_specs[$name];
    }

    public function createEntity($name, $data)
    {
        $spec = $this->getSpec($name);

        $entity = new Entity($spec, $data);

        return $entity;
    }
}