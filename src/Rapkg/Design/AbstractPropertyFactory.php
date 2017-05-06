<?php
/**
 * User: coderd
 * Date: 2017/5/6
 * Time: 下午2:27
 */

namespace Rapkg\Design;


abstract class AbstractPropertyFactory
{
    private $propertyContainer = [];

    /**
     * @return string
     */
    abstract protected function namespacePrefix();

    public function __get($name)
    {
        if (isset($this->propertyContainer[$name])) {
            return $this->propertyContainer[$name];
        }

        $className = $this->namespacePrefix() . ucfirst($name);
        if (!class_exists($className)) {
            throw new \RuntimeException(
                "Class $className not exists, you probably forget to define it?"
            );
        }
        $obj = new $className();
        $this->propertyContainer[$name] = $obj;

        return $obj;
    }
}