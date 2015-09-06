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

Provided that you have an interface, e.g. for a book, you can extend the Uuid-Aware interface;

```
#!php
<?php
use Aedart\Model\Uuid\Interfaces\UuidAware;

interface IBook extends UuidAware {

    // ... Remaining interface implementation not shown ...
    
}

```

In your concrete implementation, you simple use the uuid-trait;
 
```
#!php
<?php
use Aedart\Model\Uuid\Traits\UuidTrait;

class MyBook implements IBook {
 
    use UuidTrait;

    // ... Remaining implementation not shown ... 
 
}
```

## Acknowledgement ##



## License ##

[BSD-3-Clause](http://spdx.org/licenses/BSD-3-Clause), Read the LICENSE file included in this package