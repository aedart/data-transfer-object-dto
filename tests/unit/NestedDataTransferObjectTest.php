<?php

use Aedart\DTO\DataTransferObject;
use Aedart\DTO\Providers\Bootstrap;
use Faker\Factory;

/**
 * Class NestedDataTransferObjectTest
 *
 * @group dto
 * @coversDefaultClass Aedart\DTO\DataTransferObject
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class NestedDataTransferObjectTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Faker\Generator
     */
    protected $faker = null;

    protected function _before()
    {
        $this->faker = Factory::create();

        Bootstrap::boot();
    }

    protected function _after()
    {
        Bootstrap::destroy();
    }

    /***************************************************************
     * Utilities and helpers
     **************************************************************/

    /**
     * Get the IoC service container
     *
     * @return \Illuminate\Contracts\Container\Container
     */
    protected function getContainer(){
        return Bootstrap::getContainer();
    }

    /***************************************************************
     * Actual tests
     **************************************************************/

    /**
     * @test
     * @covers ::__set
     */
    public function canPopulateScalarProperty(){
        $data = [
            'name' => $this->faker->name
        ];

        $person = new Person($data);

        $this->assertSame($data['name'], $person->name, 'Name should had been set');
    }

    /**
     * @test
     * @covers ::__set
     */
    public function canPopulateWithNestedObjectInstance(){
        $cityData = [
            'name' => $this->faker->city,
            'zipCode' => $this->faker->postcode
        ];

        $addressData = [
            'street' => $this->faker->streetName,
            'city' => new City($cityData)
        ];

        $personData = [
            'name' => $this->faker->name,
            'address'   => new Address($addressData)
        ];

        $person = new Person($personData);

        $this->assertSame($addressData['street'], $person->address->street, 'Street should have been set');
        $this->assertSame($cityData['name'], $person->address->city->name, 'City name should have been set');
    }

    /**
     * @test
     * @covers ::__set
     */
    public function canResolveAndPopulateFromServiceContainer() {
        $container = $this->getContainer();

        // Make the bindings
        $container->bind(Address::class, function($container, $parameters){
            return new Address($parameters);
        });
        $container->bind(City::class, function($container, $parameters){
            return new City($parameters);
        });

        $personData = [
            'name' => $this->faker->name,
            'address'   => [
                'street' => $this->faker->streetName,
                'city' => [
                    'name' => $this->faker->city,
                    'zipCode' => $this->faker->postcode
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
     * @covers ::__set
     */
    public function failsResolvingWhenObjectNotBound(){
        //$container = $this->getContainer();

        // Make the bindings
//        $container->bind(Address::class, function($container, $parameters){
//            return new Address($parameters);
//        });

        // Not binding city for this test - should thus fail!
        //        $container->bind(City::class, function($container, $parameters){
        //            return new City($parameters);
        //        });

        $personData = [
            'name' => $this->faker->name,
            'address'   => [
                'street' => $this->faker->streetName,
                'city' => [
                    'name' => $this->faker->city,
                    'zipCode' => $this->faker->postcode
                ]
            ]
        ];

        $person = new Person($personData);

        dd($person);
    }
}

/**
 * Class Person (Dummy Dto)
 *
 * @property string $name
 * @property Address $address
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class Person extends DataTransferObject {

    protected $name = '';

    /**
     * @var Address
     */
    protected $address = null;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return Address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address) {
        $this->address = $address;
    }

}

/**
 * Class Address (Dummy Dto)
 *
 * @property string $street
 * @property City $city
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class Address extends DataTransferObject {

    protected $street = '';

    /**
     * @var City
     */
    protected $city = null;

    /**
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street) {
        $this->street = $street;
    }

    /**
     * @return City
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city) {
        $this->city = $city;
    }

}

/**
 * Class City (Dummy Dto)
 *
 * @property string $name
 * @property int $zipCode
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class City extends DataTransferObject {

    protected $name = '';

    protected $zipCode = 0;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    /**
     * @param int $zipCode
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
    }

}