[![Build Status](https://travis-ci.org/aedart/data-transfer-object-dto.svg?branch=master)](https://travis-ci.org/aedart/data-transfer-object-dto)
[![Latest Stable Version](https://poser.pugx.org/aedart/dto/v/stable)](https://packagist.org/packages/aedart/dto)
[![Total Downloads](https://poser.pugx.org/aedart/dto/downloads)](https://packagist.org/packages/aedart/dto)
[![Latest Unstable Version](https://poser.pugx.org/aedart/dto/v/unstable)](https://packagist.org/packages/aedart/dto)
[![License](https://poser.pugx.org/aedart/dto/license)](https://packagist.org/packages/aedart/dto)

# Data Transfer Object (DTO)

A variation / interpretation of the Data Transfer Object (DTO) design pattern (Distribution Pattern). A DTO is nothing more than an object that
can hold some data. Most commonly it is used for for transporting that data between systems, e.g. a client and a server. 

This package provides an abstraction for such DTOs.

If you don't know about DTOs, I recommend you to read [Martin Fowler's description](http://martinfowler.com/eaaCatalog/dataTransferObject.html) of DTO, and perhaps
perform a few [Google searches](https://www.google.com/search?q=data+transfer+object&ie=utf-8&oe=utf-8) about this topic.

## Contents

* [When to use this](#when-to-use-this)
* [How to install](#how-to-install)
* [Quick start](#quick-start)
* [Advanced usage](#advanced-usage)
* [Contribution](#contribution)
* [Acknowledgement](#acknowledgement)
* [Versioning](#versioning)
* [License](#license)

## When to use this

* When there is a strong need to interface DTOs, e.g. what properties must be available via getters and setters
* When you need to encapsulate data that needs to be communicated between systems and or component instances

Nevertheless, using DTOs can / will increase complexity of your project. Therefore, you should only use it, when you are really sure that you need them.

## How to install

```console
composer require aedart/dto
```

This package uses [composer](https://getcomposer.org/). If you do not know what that is or how it works, I recommend that you read a little about, before attempting to use this package.

## Quick start

### Custom Interface for your DTO

Start off by creating an interface for your DTO. Below is an example for a simple Person interface

```php
<?php
use Aedart\DTO\Contracts\DataTransferObject as DataTransferObjectInterface;

interface PersonInterface extends DataTransferObjectInterface
{
    /**
     * Set the person's name
     *
     * @param string|null $name
     */
    public function setName(?string $name);
    
    /**
     * Get the person's name
     *
     * @return string
     */
    public function getName() : ?string;
    
    /**
     * Set the person's age
     *
     * @param int $age
     */
    public function setAge(?int $age);
    
    /**
     * Get the person's age
     *
     * @return int
     */
    public function getAge() : ?int;
}
```

### Concrete implementation of your DTO

Create a concrete implementation of your interface. Let it extend the default `DataTransferObject` abstraction.
 
```php
<?php
declare(strict_types=1);

use Aedart\DTO\DataTransferObject;

class Person extends DataTransferObject implements PersonInterface
{
 
    protected $name = '';
    
    protected $age = 0;
 
    /**
     * Set the person's name
     *
     * @param string $name
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }
    
    /**
     * Get the person's name
     *
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    
    /**
     * Set the person's age
     *
     * @param int $age
     */
    public function setAge(?int $age)
    {
        $this->age = $age;
    }
    
    /**
     * Get the person's age
     *
     * @return int
     */
    public function getAge() : ?int
    {
        return $this->age;
    } 

}
```

Now you are ready to use the DTO. The following sections will highlight some of the usage scenarios. 

### Property overloading

Each defined property is accessible in multiple ways, if a getter and or setter method has been defined for that given property.

For additional information, please read about [Mutators and Accessor](https://en.wikipedia.org/wiki/Mutator_method), [PHP's overloading](http://php.net/manual/en/language.oop5.overloading.php),
and [PHP's Array-Access](http://php.net/manual/en/class.arrayaccess.php)

```php
<?php

// Create a new instance of your DTO
$person = new Person();

// Name can be set using normal setter methods
$person->setName('John');

// But you can also just set the property itself
$person->name = 'Jack'; // Will automatically invoke setName()

// And you can also set it, using an array-accessor
$person['name'] = 'Jane'; // Will also automatically invoke setName()

// ... //

// Obtain age using the regular getter method
$age = $person->getAge();

// Can also get it via invoking the property directly
$age = $person->age; // Will automatically invoke getAge()

// Lastly, it can also be access via an array-accessor
$age = $person['age']; // Also invokes the getAge()
```

#### Tip: PHPDoc's property-tag

If you are using a modern [IDE](https://en.wikipedia.org/wiki/Integrated_development_environment), then it will most likely support [PHPDoc](http://www.phpdoc.org/).

By adding a [`@property`](http://www.phpdoc.org/docs/latest/references/phpdoc/tags/property.html) tag to your interface or concrete implementation, your IDE will be able to auto-complete the overloadable properties.

### Populating via an array

You can populate your DTO using an array.

```php
<?php

// property-name => value array
$data = [
    'name' => 'Timmy Jones',
    'age'  => 32
];

// Create instance and invoke populate
$person = new Person();
$person->populate($data); // setName() and setAge() are invoked with the given values
```

If you are extending the default DTO abstraction, then you can also pass in an array in the constructor

```php
<?php

// property-name => value array
$data = [
    'name' => 'Carmen Rock',
    'age'  => 25
];

// Create instance and invoke populate
$person = new Person($data); // invokes populate(...), which then invokes the setter methods
```

### Export properties to array

Each DTO can be exported to an array.

```php
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

### Serialize to Json

All DTOs are Json serializable, meaning that they inherit from the [`JsonSerializable`](http://php.net/manual/en/class.jsonserializable.php) interface.
This means that when using `json_encode()`, the DTO automatically ensures that its properties are serializable by the encoding method.

```php
<?php

$person = new Person([
    'name' => 'Rian Dou',
    'age' => 29
]);

echo json_encode($person);
```

The above example will output the following;

``` json
{
    "name":"Rian Dou",
    "age":29
}
```

You can also perform json serialization directly on the DTO, by invoking the `toJson()` method.

```php
<?php

$person = new Person([
    'name' => 'Rian Dou',
    'age' => 29
]);

echo $person->toJson(); // The same as invoking json_encode($person);
```

## Advanced usage

### Inversion of Control (IoC) / Dependency Injection

In this interpretation of the DTO design pattern, each instance must hold a reference to an [IoC service container](http://laravel.com/docs/5.1/container).

If you do not know what this means or how this works, please start off by reading the [wiki-article](https://en.wikipedia.org/wiki/Inversion_of_control) about it.

#### Bootstrapping a service container ####

If you are using this package inside a [Laravel](http://laravel.com/) application, then you can skip this part; **it is NOT needed!**

```php
<?php

use Aedart\DTO\Providers\Bootstrap;

// Invoke the bootstrap's boot method, before using any DTOs
// Ideally, this should happen along side your application other bootstrapping logic
Bootstrap::boot(); // A default service container is now available 
```

### Nested instances

Imagine that your `Person` DTO accepts more complex properties, e.g. an address;

_NOTE_: This example will only work if;

a) You are using the DTO inside a [Laravel](http://laravel.com/) application

*or*

b) You have invoked the `Bootstrap::boot()` method, before using the given DTO (*...once again this is not needed, if you are using this package inside a Laravel application*)

```php
<?php
declare(strict_types=1);

use Aedart\DTO\DataTransferObject;

// None-interfaced DTO class is on purpose for this example
class Address extends DataTransferObject
{

    protected $street = '';

    /**
     * Set the street
     *
     * @param string $street
     */
    public function setStreet(?string $street)
    {
        $this->street = $street;
    }
    
    /**
     * Get the street
     *
     * @return string
     */
    public function getStreet() : ?string
    {
        return $this->street;
    }
}

// You Person DTO now accepts an address object
class Person extends DataTransferObject implements PersonInterface
{
 
    protected $name = '';
    
    protected $age = 0;
 
    protected $address = null;
 
    // ... getters and setters for name and age not shown ... //

     /**
      * Set the address
      *
      * @param Address $address
      */
     public function setAddress(?Address $address)
     {
         $this->address = $address;
     }
     
     /**
      * Get the address
      *
      * @return Address
      */
     public function getAddress() : ?Address
     {
         return $this->address;
     }
}

// ... some place else, in your application ... //

// Data for your Person DTO
$data = [
    'name' => 'Arial Jackson',
    'age' => 42,
    
    // Notice that we are NOT passing in an instance of Address, but an array instead!
    'address' => [
        'street' => 'Somewhere str. 44'
    ]
];

$person = new Person($data);                                    
$address = $person->getAddress();   // Instance of Address - Will automatically be resolved (if possible).
```

In the above example, [Laravel's Service Container](http://laravel.com/docs/5.5/container) attempts to find and create any concrete instances that are expected.

Furthermore, the default DTO abstraction (`Aedart\DTO\DataTransferObject`) will attempt to automatically populate that instance.

### Interface bindings

If you prefer to use interfaces instead, then you need to `bind` those interfaces to concrete instances, before the DTOs / service container can handle and resolve them.

#### Outside Laravel Application

If you are outside a Laravel application, then you can bind interfaces to concrete instances, in the following way;

```php
<?php

// Somewhere in your application's bootstrapping logic

use Aedart\DTO\Providers\Bootstrap;

// Boot up the service container
Bootstrap::boot(); 

// Register / bind your interfaces to concrete instances
Bootstrap::getContainer()->bind(CityInterface::class, function($app){
    return new City();
});
```

#### Inside Laravel Application

Inside your application's [service provider](https://laravel.com/docs/5.5/providers) (or perhaps a custom service provider), you can bind your DTO interfaces to concrete instances;

```php
<?php

// ... somewhere inside your service provider

// Register / bind your interfaces to concrete instances
$this->app->bind(CityInterface::class, function($app){
    return new City();
});
```
#### Example

Given that you have bound your interfaces to concrete instances, then the following is possible

```php
<?php
use Aedart\DTO\Contracts\DataTransferObject as DataTransferObjectInterface;
use Aedart\DTO\DataTransferObject;

// Interface for a City
interface CityInterface extends DataTransferObjectInterface
{
    /**
     * Set the city's name
     *
     * @param string $name
     */
    public function setName(string $name) : void;
    
    /**
     * Get the city's name
     *
     * @return string
     */
    public function getName() : string;
}

// Concrete implementation of City
class City extends DataTransferObject implements CityInterface
{
    protected $name = '';
    
    // ... getter and setter implementation not shown ... //
}

// Address class now also accepts a city property, of the type CityInterface
class Address extends DataTransferObject
{

    protected $street = '';

    protected $city = null;

    // ... street getter and setter implementation not shown ... //
    
     /**
      * Set the city
      *
      * @param CityInterface $address
      */
     public function setCity(?CityInterface $city)
     {
         $this->city = $city;
     }
     
     /**
      * Get the city
      *
      * @return CityInterface
      */
     public function getCity() : ?CityInterface
     {
         return $this->city;
     }
}

// ... some other place in your application ... //

$addressData = [
    'street' => 'Marshall Street 27',
    'city' => [
        'name' => 'Lincoln'
    ]
];

// Create new instance and populate
$address = new Address($addressData);   // Will attempt to automatically resolve the expected city property,
                                        // of the CityInterface type, by creating a concrete City, using
                                        // the service container, and resolve the bound interface instance
```

## Contribution

Have you found a defect ( [bug or design flaw](https://en.wikipedia.org/wiki/Software_bug) ), or do you wish improvements? In the following sections, you might find some useful information
on how you can help this project. In any case, I thank you for taking the time to help me improve this project's deliverables and overall quality.

### Bug Report

If you are convinced that you have found a bug, then at the very least you should create a new issue. In that given issue, you should as a minimum describe the following;

* Where is the defect located
* A good, short and precise description of the defect (Why is it a defect)
* How to replicate the defect
* (_A possible solution for how to resolve the defect_)

When time permits it, I will review your issue and take action upon it.

### Fork, code and send pull-request

A good and well written bug report can help me a lot. Nevertheless, if you can or wish to resolve the defect by yourself, here is how you can do so;

* Fork this project
* Create a new local development branch for the given defect-fix
* Write your code / changes
* Create executable test-cases (prove that your changes are solid!)
* Commit and push your changes to your fork-repository
* Send a pull-request with your changes
* _Drink a [Beer](https://en.wikipedia.org/wiki/Beer) - you earned it_ :)

As soon as I receive the pull-request (_and have time for it_), I will review your changes and merge them into this project. If not, I will inform you why I choose not to.

## Acknowledgement

* [Martin Fowler](http://martinfowler.com/aboutMe.html), for sharing his knowledge about [DTOs](http://martinfowler.com/eaaCatalog/dataTransferObject.html) and many other design patterns
* [Taylor Otwell](https://github.com/taylorotwell), for creating [Laravel](https://laravel.com) and especially the [Service Container](https://laravel.com/docs/5.4/container), that I'm using daily
* [Jeffrey Way](https://github.com/JeffreyWay), for creating [Laracasts](https://laracasts.com/) - a great place to learn new things... And where I finally understood some of the principles of IoC!

## Versioning

This package follows [Semantic Versioning 2.0.0](http://semver.org/)

## License

[BSD-3-Clause](http://spdx.org/licenses/BSD-3-Clause), Read the LICENSE file included in this package