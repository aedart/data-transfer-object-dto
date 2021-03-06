<?php

use Aedart\DTO\Providers\Bootstrap;
use Aedart\Testing\TestCases\Unit\UnitTestCase;
use Illuminate\Contracts\Container\Container;

/**
 * Class NestedDataTransferObjectTest
 *
 * @group dto
 * @group dto-nested
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class NestedDataTransferObjectTest extends UnitTestCase
{

    protected function _before()
    {
        parent::_before();

        Bootstrap::boot();
    }

    protected function _after()
    {
        Bootstrap::destroy();

        parent::_after();
    }

    /***************************************************************
     * Utilities and helpers
     **************************************************************/

    /**
     * Get the IoC service container
     *
     * @return Container|null
     */
    protected function getContainer() : ?Container
    {
        return Bootstrap::getContainer();
    }

    /***************************************************************
     * Actual tests
     **************************************************************/

    /**
     * @test
     */
    public function canPopulatePropertyOfPrimitiveType()
    {
        $data = [
            'name' => $this->faker->name
        ];

        $person = new Person($data);

        $this->assertSame($data['name'], $person->name, 'Name should had been set');
    }

    /**
     * @test
     */
    public function canPopulateWithNestedObjectInstance()
    {
        $cityData = [
            'name' => $this->faker->city,
            'zipCode' => (int) $this->faker->postcode
        ];

        $addressData = [
            'street' => $this->faker->streetName,
            'city' => new City($cityData)
        ];

        $personData = [
            'name' => $this->faker->name,
            'address' => new Address($addressData)
        ];

        $person = new Person($personData);

        $this->assertSame($addressData['street'], $person->address->street, 'Street should have been set');
        $this->assertSame($cityData['name'], $person->address->city->name, 'City name should have been set');
    }

    /**
     * Please note: because we are using Laravel's default container, it
     * can handle creating instances of concrete classes that are expected.
     * Thus, there is no need to `bind` them, in this case
     *
     * @test
     */
    public function canResolveAndPopulateUnboundConcreteInstances()
    {
        $personData = [
            'name' => $this->faker->name,
            'address' => [
                'street' => $this->faker->streetName,
                'city' => [
                    'name' => $this->faker->city,
                    'zipCode' => (int) $this->faker->postcode
                ]
            ]
        ];

        $person = new Person($personData);

        $this->assertSame($personData['name'], $person->name, 'Name of person is invalid');
        $this->assertSame($personData['address']['street'], $person->address->street, 'Street should have been set');
        $this->assertSame($personData['address']['city']['name'], $person->address->city->name, 'City name should have been set');
    }

    /**
     * @test
     *
     * @expectedException \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function failWhenNoServiceContainerIsAvailable()
    {
        Bootstrap::destroy();

        $personData = [
            'name' => $this->faker->name,
            'address' => [
                'street' => $this->faker->streetName,
                'city' => [
                    'name' => $this->faker->city,
                    'zipCode' => (int) $this->faker->postcode
                ]
            ]
        ];

        $person = new Person($personData);
    }

    /**
     * @test
     */
    public function canSerialiseNestedInstances()
    {
        $personData = [
            'name' => $this->faker->name,
            'address' => [
                'street' => $this->faker->streetName,
                'city' => [
                    'name' => $this->faker->city,
                    'zipCode' => (int) $this->faker->postcode
                ]
            ]
        ];

        $person = new Person($personData);

        $serialized = json_encode($person);

        $this->assertJson($serialized, 'Could not serialise nested instances');
    }

    /**
     * @test
     */
    public function canResolveUsingOverloadMethodDirectly()
    {
        $person = new Person();

        $data = [
            'street' => $this->faker->streetName,
            'city' => [
                'name' => $this->faker->city,
                'zipCode' => (int) $this->faker->postcode
            ]
        ];

        $person->address = $data;

        $this->assertSame($data['city']['zipCode'], $person->address->city->zipCode,
            'ZipCode was expected to be of a different value!');
    }

    /**
     * @test
     *
     * @expectedException \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function failsPopulatingUnboundAbstractInstances()
    {
        $personData = [
            'name' => $this->faker->name,
            'address' => [
                'street' => $this->faker->streetName,
                'city' => [
                    'name' => $this->faker->city,
                    'zipCode' => (int) $this->faker->postcode
                ]
            ],

            // Person expects a type of `NotesInterface`
            // which in this test has NOT been bound, thus
            // this should fail - Laravel's container should
            // make sure of that
            'notes' => [
                'notes' => [
                    $this->faker->sentence,
                    $this->faker->sentence,
                    $this->faker->sentence,
                ]
            ]
        ];

        $person = new Person($personData);
    }

    /**
     * @test
     */
    public function canResolveAndPopulateBoundAbstractInstances()
    {

        // Bind the abstraction / interface
        $this->getContainer()->bind(NotesInterface::class, function ($app) {
            return new Notes();
        });

        $personData = [
            'name' => $this->faker->name,
            'address' => [
                'street' => $this->faker->streetName,
                'city' => [
                    'name' => $this->faker->city,
                    'zipCode' => (int) $this->faker->postcode
                ]
            ],

            // Here, the interface is bound, thus this should
            // not fail
            'notes' => [
                'notes' => [
                    $this->faker->sentence,
                    $this->faker->sentence,
                    $this->faker->sentence,
                ]
            ]
        ];

        $person = new Person($personData);

        $arr = $personData['notes']['notes'];
        $actualNotes = $person->notes->getNotes();
        foreach($arr as $value){
            $result = in_array($value, $actualNotes);
            $this->assertTrue($result, 'A value was not in the notes list');
        }
    }

    /**
     * In this test, the given `badInstance` property is of the concrete type
     * `BadUnpopulatableObject`, which does not inherit from `Populatable`
     * interface and thus we do not know how to populate it and should fail!
     *
     * @test
     *
     * @expectedException \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function failsResolvingConcreteUnpopulatableInstance()
    {
        $personData = [
            'badInstance' => [
                'foo' => 'bar'
            ]
        ];

        $person = new Person($personData);
    }

    /**
     * In this test, we should be able to populate `Person`, with
     * the `BadUnpopulatableObject`, when given as a concrete instance.
     *
     * <br />
     *
     * <b>WARNING</b>: You should avoid creating your DTOs without
     * inheritance from the `Populatable` and `Arrayable` interfaces
     * (minimum requirements).
     *
     * @see canPopulateWithNestedObjectInstance Similar test!
     *
     * @test
     */
    public function canPopulateWithConcreteBadInstance()
    {
        $foo = $this->faker->word;

        $badInstance = new BadUnpopulatableObject();
        $badInstance->setFoo($foo);

        $personData = [
            'badInstance' => $badInstance
        ];

        $person = new Person($personData);

        $this->assertSame($foo, $person->badInstance->getFoo(), 'Foor should had been set');
    }
}