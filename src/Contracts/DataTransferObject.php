<?php namespace Aedart\DTO\Contracts;

use Aedart\Overload\Interfaces\PropertyOverloadable;
use Aedart\Util\Interfaces\Populatable;
use ArrayAccess;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * <h1>Interface Data Transfer Object</h1>
 *
 * Variation / Interpretation of the Data Transfer Object (DTO) design pattern (Distribution Pattern).
 * A DTO is responsible for;
 *
 * <ul>
 *      <li>Holding data (properties / attributes) for remote calls, e.g. client server communication</li>
 *      <li>Be serializable</li>
 *      <li>Contain NO additional behaviour, e.g. business logic</li>
 * </ul>
 *
 * <h2>Getters and Setters</h2>
 *
 * This DTO ensures that its belonging properties / attributes can be <b>overloaded</b>,
 * if those properties / attributes have corresponding <b>getters and setters</b> (accessors and mutators).
 *
 * <h2>Serialization</h2>
 *
 * In this variation of the DTO, <b>serialization defaults to Json</b>.
 *
 * <h2>Inversion of Control / Dependency Inversion</h>
 *
 * Each DTO holds an instance of a Inversion of Control (<b>IoC</b>) service container, which can be
 * used for resolving nested dependencies, when populating the DTO with data. E.g. when a DTO's property is a
 * class object instance type. However, this is implementation specific.
 *
 * <h2>When to use DTOs</h2>
 *
 * <ul>
 *      <li>When there is a strong need to interface DTOs, e.g. what properties must be available via getters and setters</li>
 *      <li>When you need to encapsulate data that needs to be communicated between systems and or component instances</li>
 * </ul>
 *
 * There are probably many more reasons why and when you should use DTOs. However, you should know that <b>using DTOs can / will
 * increase complexity of your project!</b>
 *
 * @see http://martinfowler.com/eaaCatalog/dataTransferObject.html
 * @see https://en.wikipedia.org/wiki/Data_transfer_object
 * @see https://en.wikipedia.org/wiki/Mutator_method
 * @see http://php.net/manual/en/language.oop5.overloading.php
 * @see http://php.net/manual/en/class.arrayaccess.php
 * @see https://en.wikipedia.org/wiki/Inversion_of_control
 * @see http://laravel.com/docs/5.1/container
 * @see http://php.net/manual/en/class.jsonserializable.php
 * @see PropertyOverloadable
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\DTO\Contracts
 */
interface DataTransferObject extends PropertyOverloadable, ArrayAccess, Arrayable, Jsonable, JsonSerializable, Populatable{

    /**
     * Returns a list of the properties / attributes that
     * this Data Transfer Object can be populated with
     *
     * @return string[]
     */
    public function populatableProperties();

    /**
     * Returns the container that is responsible for
     * resolving dependency injection or eventual
     * nested object
     *
     * @return Container IoC service Container
     */
    public function container();
}