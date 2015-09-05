<?php namespace Aedart\DTO\Contracts;

use Aedart\Overload\Interfaces\PropertyOverloadable;
use Aedart\Util\Interfaces\Populatable;
use ArrayAccess;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Interface DataTransferObject
 *
 * TODO: Description of what a DTO actually is...
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