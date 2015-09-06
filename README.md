[![Latest Stable Version](https://poser.pugx.org/aedart/dto/v/stable)](https://packagist.org/packages/aedart/dto)
[![Total Downloads](https://poser.pugx.org/aedart/dto/downloads)](https://packagist.org/packages/aedart/dto)
[![Latest Unstable Version](https://poser.pugx.org/aedart/dto/v/unstable)](https://packagist.org/packages/aedart/dto)
[![License](https://poser.pugx.org/aedart/dto/license)](https://packagist.org/packages/aedart/dto)
[![Monthly Downloads](https://poser.pugx.org/aedart/dto/d/monthly)](https://packagist.org/packages/aedart/dto)
[![Daily Downloads](https://poser.pugx.org/aedart/dto/d/daily)](https://packagist.org/packages/aedart/dto)

## Data Transfer Object (DTO) ##

A variation / interpretation of the Data Transfer Object (DTO) design pattern (Distribution Pattern). A DTO is nothing more than an object that
can hold some data. Most commonly it is used for for transporting that data between systems, e.g. a client and a server. 

This package provides an abstraction for such DTOs.

## Contents ##

[TOC]

## When to use this ##

* When there is a strong need to interface DTOs, e.g. what properties must be available via getters and setters
* When you need to encapsulate data that needs to be communicated between systems and or component instances

Nevertheless, using DTOs can / will increase complexity of your project. Therefore, you should only use it, when you are really sure that you need them.

## How to install ##

```
#!console

composer require aedart/dto
```

This package uses [composer](https://getcomposer.org/). If you do not know what that is or how it works, I recommend that you read a little about, before attempting to use this package.

## Quick start ##

### Custom Interface for your DTO ###

Start off by creating an interface for your DTO. Below is an example for a simple Person interface

```
#!php
<?php
use Aedart\DTO\Contracts\DataTransferObject as DataTransferObjectInterface

interface PersonInterface extends DataTransferObjectInterface {

    /**
     * Set the person's name
     *
     * @param string $name
     */
    public function setName($name);
    
    /**
     * Get the person's name
     *
     * @return string
     */
    public function getName();
    
    /**
     * Set the person's age
     *
     * @param int $age
     */
    public function setAge($age);
    
    /**
     * Get the person's age
     *
     * @return int
     */
    public function getAge();
}

```

### Concrete implementation of your DTO ###

Create a concrete implementation of your interface. Let it extend the default `DataTransferObject` abstraction.
 
```
#!php
<?php
use Aedart\DTO\DataTransferObject;

class Person extends DataTransferObject implements PersonInterface {
 
    protected $name = '';
    
    protected $age = 0;
 
    /**
     * Set the person's name
     *
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
    }
    
    /**
     * Get the person's name
     *
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * Set the person's age
     *
     * @param int $age
     */
    public function setAge($age){
        $this->age = $age;
    }
    
    /**
     * Get the person's age
     *
     * @return int
     */
    public function getAge(){
        return $this->age;
    } 
 
}
```

Now you are ready to use the DTO. The following sections will highlight some of the usage scenarios. 

### Property overloading ###

Each defined property is accessible in multiple ways, if a getter and or setter method has been defined for that given property.

For additional information, please read about [Mutators and Accessor](https://en.wikipedia.org/wiki/Mutator_method), [PHP's overloading](http://php.net/manual/en/language.oop5.overloading.php),
and [PHP's Array-Access](http://php.net/manual/en/class.arrayaccess.php)

```
#!php
<?php

// Create a new instance of your DTO
$person = new Person();

// Name can be set using normal setter methods
$person->setName('John');

// But you can also just set the property itself
$person->name = 'Jack' // Will automatically invoke setName()

// And you can also set it, using an array-accessor
$person['name'] = 'Jane' // Will also automatically invoke setName()

// ... //

// Obtain age using the regular getter method
$age = $person->getAge();

// Can also get it via invoking the property directly
$age = $person->age; // Will automatically invoke getAge()

// Lastly, it can also be access via an array-accessor
$age = $person['age']; // Also invokes the getAge()

```

### Populating via an array ###

You can populate your DTO using an array.

```
#!php
<?php

// property-name => value array
$data = [
    'name' => 'Timmy Jones',
    'age'  => 32
];

// Create instance and invoke populate
$person = new Person();
$person->populate($data); // setName() and setAge() is invoked with the given values

```

If you are extending the default DTO abstraction, then you can also pass in an array in the constructor

```
#!php
<?php

// property-name => value array
$data = [
    'name' => 'Carmen Rock',
    'age'  => 25
];

// Create instance and invoke populate
$person = new Person($data); // invokes populate(...), which then invokes the setter methods

```

### Export properties to array ###

Each DTO can be exported to an array.

```
#!php
<?php

// Provided that you have a populated instance, you can export those properties to an array 
$properties = $person->toArray();

var_dump($properties);  // Will output a "property-name => value" list
                        // Example:
                        //  [
                        //      'name'  => 'Timmy'
                        //      'age'   => 16
                        //  ]

```

### Serialize to Json ###

All DTOs are Json serializable, meaning that they inherit from the [`JsonSerializable`](http://php.net/manual/en/class.jsonserializable.php) interface.
This means that when using `json_encode()`, the DTO automatically ensures that its properties are serializable by the encoding method.

```
#!php
<?php

$person = new Person([
    'name' => 'Rian Dou',
    'age' => 29
]);

echo json_encode($person);

```

The above example will output the following;

```
#!json
{
    "name":"Rian Dou",
    "age":29
}
```



## Acknowledgement ##



## License ##

[BSD-3-Clause](http://spdx.org/licenses/BSD-3-Clause), Read the LICENSE file included in this package