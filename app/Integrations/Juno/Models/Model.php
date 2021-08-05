<?php


namespace App\Integrations\Juno\Models;


use App\Integrations\Juno\Contracts\Arrayable;

abstract class Model implements Arrayable
{
    const DATE_FORMAT = 'Y-m-d';

    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $array = [];
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property) {
            $propertyName = $property->getName();
            $getterMethod = "get" . ucfirst($propertyName);
            $hasGetter = $reflection->hasMethod($getterMethod);
            if($hasGetter) {
                $array[$propertyName] = $this->$getterMethod();
            } else {
                $array[$propertyName] = $this->$propertyName;
            }
        }

        return $array;
    }

    public function clear($subject)
    {
        return preg_replace('/\.|\-|\//', '', $subject);
    }
}
