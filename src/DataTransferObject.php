<?php namespace Aedart\DTO;

use Aedart\DTO\Contracts\DataTransferObject as DataTransferObjectInterface;
use Aedart\Overload\Traits\PropertyOverloadTrait;
use Aedart\Util\Interfaces\Populatable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\App;
use ReflectionClass;
use ReflectionParameter;

/**
 * <h1>Abstract Data Transfer Object</h1>
 *
 * This DTO abstraction offers default implementation of the following;
 *
 * <ul>
 *      <li>Overloading of properties, if they have getters and setters defined</li>
 *      <li>Array-accessibility of properties, if properties have getters and setters defined</li>
 *      <li>Population of properties via array</li>
 *      <li>Resolving nested dependencies, via a IoC service container, if one is available</li>
 *      <li>Exportation of properties to an array</li>
 *      <li>Serialization of properties to json</li>
 * </ul>
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

    public function __set($name, $value) {

        $resolvedValue = $value;

        $methodName = $this->generateSetterName($name);
        if($this->hasInternalMethod($methodName)){
            $resolvedValue = $this->resolveValue($methodName, $value);
        }

        $this->__setFromTrait($name, $resolvedValue);
    }

    /**
     * Resolve and return the given value, for the given setter method
     *
     * @param string $setterMethodName The setter method to be invoked
     * @param mixed $value The value to be passed to the setter method
     *
     * @return mixed
     */
    protected function resolveValue($setterMethodName, $value){
        $reflection = new ReflectionClass($this);

        $method = $reflection->getMethod($setterMethodName);

        $parameter = $method->getParameters()[0];

        return $this->resolveParameter($parameter, $value);
    }

    /**
     * Resolve the given parameter; pass the given value to it
     *
     * @param ReflectionParameter $parameter The setter method's parameter reflection
     * @param mixed $value The value to be passed to the setter method
     *
     * @return mixed
     * @throws BindingResolutionException   a) If no concrete instance could be resolved from the IoC, or
     *                                      b) If the instance is not populatable and or the given value is not an
     *                                      array that can be passed to the populatable instance
     *                                      c) No service container is available
     */
    protected function resolveParameter(ReflectionParameter $parameter, $value){

        // If there is no class for the given parameter
        // then some kind of primitive data has been provided
        // and thus we need only to return it.
        if(is_null($parameter->getClass())){
            return $value;
        }

        $className = $parameter->getClass()->getName();

        // If the value corresponds to the given expected class,
        // then there is no need to resolve anything from the
        // IoC service container.
        if($value instanceof $className){
            return $value;
        }

        $container = $this->container();

        // Fail if no service container is available
        if(is_null($container)){
            $message = sprintf(
                'No IoC service container is available, cannot resolve property "%s" of the type "%s"; do not know how to populate with "%s"',
                $parameter->getName(),
                $className,
                var_export($value, true)
            );
            throw new BindingResolutionException($message);
        }

        // Get the resolved instance for the IoC container
        $instance = $container->make($className, $value);

        // At this point, we could return the resolved instance. Yet,
        // if the resolved instance was a "concrete" class, it means
        // that it's properties / constructor's arguments might not
        // have been resolved correctly. This can happen if the given
        // instance's constructor accepts arguments, but contain
        // default values, in which case the default Laravel container
        // will result to using those default values, instead of
        // the ones provided. This is not a mistake, but rather a very
        // clever way of dealing with such issues.
        //
        // For further reference, please review the default container
        // method `resolveNonClass`, in \Illuminate\Container\Container
        //
        // In our case, if an instance of this abstraction has been
        // resolved, then its default constructor values are used, which
        // causes empty objects. Therefore, the way that we deal with
        // this, if by checking if the given class was `bound` in the
        // service container. If not, then we attempt to handle this
        // by checking if its an instance of something we can populate.
        if(!$this->container()->bound($className)){
            return $this->resolveUnboundInstance($instance, $parameter, $value);
        }

        return $instance;
    }

    /**
     * Resolve an unbund instance - attempt to populate the given instance with the
     * specified value
     *
     * @param object $instance The instance that must be populated
     * @param ReflectionParameter $parameter Setter method's parameter reflection that requires the given instance
     * @param mixed $value The value to be passed to the setter method
     *
     * @return mixed
     * @throws BindingResolutionException If the instance is not populatable and or the given value is not an
     *                                      array that can be passed to the populatable instance
     */
    protected function resolveUnboundInstance($instance, ReflectionParameter $parameter, $value){

        // Check if instance is populatable and if the given value
        // is an array.
        if($instance instanceof Populatable && is_array($value)){
            $instance->populate($value);

            return $instance;
        }

        // If we reach this part, then we are simply going to fail.
        // It is NOT safe to continue and make assumptions on how
        // we can populate the given instance. For this reason, we
        // just throw an exception
        $message = sprintf(
            'Unable to resolve dependency for property "%s" of the type "%s"; do not know how to populate with "%s"',
            $parameter->getName(),
            $parameter->getClass()->getName(),
            var_export($value, true)
        );

        throw new BindingResolutionException($message);
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