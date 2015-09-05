<?php namespace Aedart\DTO;

use Aedart\DTO\Contracts\DataTransferObject as DataTransferObjectInterface;
use Aedart\Overload\Traits\PropertyOverloadTrait;
use ReflectionClass;

/**
 * Abstract Data Transfer Object
 *
 * TODO: what does this abstraction deliver
 *
 * @see \Aedart\DTO\Contracts\DataTransferObject
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\DTO
 */
abstract class DataTransferObject implements DataTransferObjectInterface {

    use PropertyOverloadTrait;

    /**
     * Create a new instance of this Data Transfer Object
     *
     * @param array $data [optional] This object's properties / attributes
     */
    public function __construct(array $data = []) {
        $this->populate($data);
    }

    public function populatableProperties(){
        $reflection = new ReflectionClass($this);

        $properties = $reflection->getProperties();

        $output = [];

        foreach($properties as $reflectionProperty){
            $name = $reflectionProperty->getName();
            $getterMethod = $this->generateGetterName($name);

            if($this->hasInternalMethod($getterMethod)){
                $output[] = $name;
            }
        }

        return $output;
    }

    public function populate(array $data) {
        if(empty($data)){
            return;
        }

        foreach($data as $name => $value){
            $this->__set($name, $value);
        }
    }

    public function toArray() {

        $properties = $this->populatableProperties();
        $output = [];

        foreach($properties as $property){
            // Make sure that property is not unset
            if(!isset($this->$property)){
                continue;
            }

            $output[$property] = $this->__get($property);
        }

        return $output;
    }

    public function offsetExists($offset) {
        return isset($this->$offset);
    }

    public function offsetGet($offset) {
        return $this->$offset;
    }

    public function offsetSet($offset, $value) {
        $this->$offset = $value;
    }

    public function offsetUnset($offset) {
        unset($this->$offset);
    }

    public function toJson($options = 0) {
        return json_encode($this->jsonSerialize(), $options);
    }

    function jsonSerialize() {
        return $this->toArray();
    }

    /**
     * Returns a string representation of this Data Transfer Object
     *
     * @return string String representation of this data transfer object
     */
    public function __toString() {
        return $this->toJson();
    }

    /**
     * Method is invoked by `var_dump()`
     *
     * <br />
     *
     * By default, this method will NOT display the `_propertyAccessibilityLevel`
     * property. This property is an internal behavioural modifier (state),
     * that should not be used, unless very important / special case.
     *
     * @see \Aedart\Overload\Traits\Helper\PropertyAccessibilityTrait
     *
     * @return array All the available properties of this Data Transfer Object
     */
    public function __debugInfo(){
        return $this->toArray();
    }
}