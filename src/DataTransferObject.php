<?php namespace Aedart\DTO;

use Aedart\DTO\Contracts\DataTransferObject as DataTransferObjectInterface;
use Aedart\Overload\Traits\PropertyOverloadTrait;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\App;
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

    use PropertyOverloadTrait {
        __set as __setFromTrait;
    }

    /**
     * Container that must resolve
     * dependency injection, should it be
     * needed
     *
     * @var Container|null The IoC service container
     */
    private $ioc = null;

    /**
     * Create a new instance of this Data Transfer Object
     *
     * <br />
     *
     * <b>IoC Service Container</b>: If no container is provided, a default
     * service container is attempted to be resolved, using a application
     * facade.
     *
     * @see \Aedart\DTO\Providers\Bootstrap
     * @see \Illuminate\Contracts\Container\Container
     * @see http://laravel.com/docs/5.1/container#introduction
     *
     * @param array $data [optional] This object's properties / attributes
     * @param Container $container [optional] Eventual container that is responsible for resolving dependency injection
     */
    public function __construct(array $data = [], Container $container = null) {
        $this->populate($data);
        $this->ioc = $container;
    }

    public function container() {
        if(is_null($this->ioc)){
            $this->ioc = App::getFacadeApplication();
        }

        return $this->ioc;
    }

    protected function resolveFromContainer(ReflectionClass $reflection, $value){
        $className = $reflection->getName();

        // If the value corresponds to the given expected class,
        // then there is no need to resolve anything from the
        // IoC service container.
        if($value instanceof $className){
            return $value;
        }

        // TODO: Default container so powerfull that it will attempt to
        // TODO: create instance of class - yet fails to populate...
        // TODO: This is by far the safest option...
        if(!$this->container()->bound($className)){
            throw new \Exception('SHOULD FAIL!');
        }

        return $this->container()->make($className, $value);

        // TODO: The problem; Container::getConcrete()

        // If we don't have a registered resolver or concrete for the type, we'll just
        // assume each type is a concrete name and will attempt to resolve it as is
        // since the container should be able to resolve concretes automatically.

        // TODO: Perhaps play a bit with populatable interface... or maybe Data Transfer Object Interface
    }

    protected function resolveValue($getterMethodName, $value){
        $reflection = new ReflectionClass($this);

        $method = $reflection->getMethod($getterMethodName);

        $parameter = $method->getParameters()[0];

        if($propertyReflectionClass = $parameter->getClass()){
            return $this->resolveFromContainer($propertyReflectionClass, $value);
        }

        return $value;
    }

    public function __set($name, $value) {

        $resolvedValue = $value;

        $methodName = $this->generateSetterName($name);
        if($this->hasInternalMethod($methodName)){
            $resolvedValue = $this->resolveValue($methodName, $value);
        }

        $this->__setFromTrait($name, $resolvedValue);
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