<?php
declare(strict_types=1);

namespace Aedart\DTO;

use Aedart\DTO\Contracts\DataTransferObject as DataTransferObjectInterface;
use Aedart\Overload\Traits\PropertyOverloadTrait;
use Aedart\Util\Contracts\Populatable;
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
abstract class DataTransferObject implements DataTransferObjectInterface
{
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
    public function __construct(array $data = [], ?Container $container = null)
    {
        $this->ioc = $container;

        $this->populate($data);
    }

    /**
     * {@inheritdoc}
     */
    public function container() : ?Container
    {
        if( ! isset($this->ioc)){
            $this->ioc = App::getFacadeApplication();
        }

        return $this->ioc;
    }

    /**
     * {@inheritdoc}
     */
    public function __set(string $name, $value)
    {
        $resolvedValue = $value;

        $methodName = $this->generateSetterName($name);
        if ($this->hasInternalMethod($methodName)) {
            $resolvedValue = $this->resolveValue($methodName, $value);
        }

        $this->__setFromTrait($name, $resolvedValue);
    }

    /**
     * {@inheritdoc}
     */
    public function populatableProperties() : array
    {
        $reflection = new ReflectionClass($this);

        $properties = $reflection->getProperties();

        $output = [];

        foreach ($properties as $reflectionProperty) {
            $name = $reflectionProperty->getName();
            $getterMethod = $this->generateGetterName($name);

            if ($this->hasInternalMethod($getterMethod)) {
                $output[] = $name;
            }
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(array $data = []) : void
    {
        foreach ($data as $name => $value) {
            $this->__set($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {

        $properties = $this->populatableProperties();
        $output = [];

        foreach ($properties as $property) {
            // Make sure that property is not unset
            if (!isset($this->$property)) {
                continue;
            }

            $output[$property] = $this->__get($property);
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Returns a string representation of this Data Transfer Object
     *
     * @return string String representation of this data transfer object
     */
    public function __toString() : string
    {
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
    public function __debugInfo() : array
    {
        return $this->toArray();
    }

    /***********************************************************************
     * Internal Methods
     **********************************************************************/

    /**
     * Resolve and return the given value, for the given setter method
     *
     * @param string $setterMethodName The setter method to be invoked
     * @param mixed $value The value to be passed to the setter method
     *
     * @return mixed
     */
    protected function resolveValue(string $setterMethodName, $value)
    {
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
    protected function resolveParameter(ReflectionParameter $parameter, $value)
    {
        // If there is no class for the given parameter
        // then some kind of primitive data has been provided
        // and thus we need only to return it.
        $paramClass = $parameter->getClass();
        if ( ! isset($paramClass)) {
            return $value;
        }

        // Fetch the name of the class
        $className = $paramClass->getName();

        // If the value corresponds to the given expected class,
        // then there is no need to resolve anything from the
        // IoC service container.
        if ($value instanceof $className) {
            return $value;
        }

        $container = $this->container();

        // Fail if no service container is available
        if (is_null($container)) {
            $message = sprintf(
                'No IoC service container is available, cannot resolve property "%s" of the type "%s"; do not know how to populate with "%s"',
                $parameter->getName(),
                $className,
                var_export($value, true)
            );
            throw new BindingResolutionException($message);
        }

        // Get the resolved instance for the IoC container
        $instance = $container->make($className);

        // From Laravel 5.4, the Container::make method no longer accepts
        // parameters, which is really sad... Nevertheless, we attempt to
        // resolve this, simply by checking if the instance can be
        // populated with the given value, which is exactly what the
        // `resolveInstancePopulation` method does.
        return $this->resolveInstancePopulation($instance, $parameter, $value);
    }

    /**
     * Attempts to populate instance, if possible
     *
     * @param object $instance The instance that must be populated
     * @param ReflectionParameter $parameter Setter method's parameter reflection that requires the given instance
     * @param mixed $value The value to be passed to the setter method
     *
     * @return mixed
     * @throws BindingResolutionException If the instance is not populatable and or the given value is not an
     *                                      array that can be passed to the populatable instance
     */
    protected function resolveInstancePopulation($instance, ReflectionParameter $parameter, $value)
    {

        // Check if instance is populatable and if the given value
        // is an array.
        if ($instance instanceof Populatable && is_array($value)) {
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
}