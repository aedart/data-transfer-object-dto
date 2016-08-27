<?php

use Aedart\DTO\DataTransferObject;
use Codeception\Util\Debug;

/**
 * Class DataTransferObjectTest
 *
 * @group dto
 * @coversDefaultClass Aedart\DTO\DataTransferObject
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class DataTransferObjectTest extends UnitTestCase
{

    /***************************************************************
     * Utilities and helpers
     **************************************************************/

    /***************************************************************
     * Actual tests
     **************************************************************/

    /**
     * @test
     * @covers ::__construct
     * @covers ::populate
     */
    public function canCreateInstanceWithoutArguments()
    {
        try {
            $dto = new DummyDto();

            $this->assertTrue(true, 'Instance created');
        } catch (Exception $e) {
            $this->fail('Cannot create instance without arguments;' . PHP_EOL . $e);
        }
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::populate
     */
    public function canCreateInstanceWithArguments()
    {
        $data = [
            'age' => $this->faker->randomDigit
        ];

        try {
            $dto = new DummyDto($data);

            $this->assertTrue(true, 'Instance created');
        } catch (Exception $e) {
            $this->fail('Cannot create instance with arguments;' . PHP_EOL . $e);
        }
    }

    /**
     * @test
     * @covers ::populatableProperties
     */
    public function canObtainPopulatableProperties()
    {
        $dto = new DummyDto();

        $populatableProperties = $dto->populatableProperties();

        Debug::debug($populatableProperties);

        $expectedList = ['name', 'age'];

        $this->assertInternalType('array', $populatableProperties, 'Array was expected');
        $this->assertSame($expectedList, $populatableProperties, 'Invalid list of populatable properties returned');
    }

    /**
     * @test
     * @covers ::populate
     */
    public function hasPopulatedCorrectly()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        $this->assertSame($data['name'], $dto->name, 'Name is incorrect');
        $this->assertSame($data['age'], $dto->age, 'Age is incorrect');
    }

    /**
     * @test
     * @covers ::offsetExists
     */
    public function canDetermineIfOffsetExistsOrNot()
    {
        $dto = new DummyDto();

        $this->assertTrue(isset($dto['name']), 'Name exists');
        $this->assertTrue(isset($dto['age']), 'Age exists');
        $this->assertFalse(isset($dto['unknownProperty']), 'Unknown property should NOT Exist');
    }

    /**
     * @test
     * @covers ::offsetGet
     */
    public function canGetViaOffset()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        $this->assertSame($data['name'], $dto['name'], 'Could not get "name" via offset');
    }

    /**
     * @test
     * @covers ::offsetGet
     *
     * @expectedException \Aedart\Overload\Exception\UndefinedPropertyException
     */
    public function failsWhenOffsetDoesNotExist()
    {
        $dto = new DummyDto();

        $something = $dto['offsetThatDoesNotExist'];
    }

    /**
     * @test
     * @covers ::offsetSet
     */
    public function canSetViaOffset()
    {
        $dto = new DummyDto();

        $name = $this->faker->name;

        $dto['name'] = $name;

        $this->assertSame($name, $dto->name, 'Name was not set correctly via offset');
    }

    /**
     * @test
     * @covers ::offsetSet
     *
     * @expectedException \Aedart\Overload\Exception\UndefinedPropertyException
     */
    public function failsSettingViaOffsetAndPropertyDoesNotExist()
    {
        $dto = new DummyDto();

        $dto['offsetThatDoesNotExist'] = $this->faker->address;
    }

    /**
     * @test
     * @covers ::offsetUnset
     */
    public function canUnsetViaOffset()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        unset($dto['age']);

        $this->assertFalse(isset($dto['age']), 'Age should be unset and no longer available');
    }

    /**
     * @test
     * @covers ::offsetUnset
     * @covers ::offsetSet
     */
    public function reassignValueToUnsetProperty()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        unset($dto['age']);

        $newAge = $this->faker->randomDigit;

        $dto['age'] = $newAge;

        $this->assertSame($newAge, $dto->age, 'Age should have a new value');
    }

    /**
     * @test
     * @covers ::jsonSerialize
     */
    public function returnsDataThatCanBeSerialisedToJson()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        $serialised = json_encode($dto);

        $this->assertNotFalse($serialised, 'Could not serialise to json');
        $this->assertJson($serialised, 'Serialised data is NOT json');
    }

    /**
     * @test
     * @covers ::toJson
     */
    public function canSerialiseToJson()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        $this->assertJson($dto->toJson(), 'Could not serialise to json');
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function canGetStringRepresentationOfDto()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        $this->assertInternalType('string', $dto->__toString(), 'toString does NOT return a string!');
    }

    /**
     * @test
     * @covers ::__debugInfo
     * @covers ::toArray
     */
    public function debugInformationDoesNotContainSpecialProperty()
    {
        $dto = new DummyDto();

        $debugInformation = $dto->__debugInfo();

        Debug::debug($debugInformation);

        $keys = array_keys($debugInformation);

        $this->assertNotContains('_propertyAccessibilityLevel', $keys);
    }

    /**
     * @test
     * @covers ::__debugInfo
     * @covers ::toArray
     */
    public function debugInformationDoesNotContainUnsetProperties()
    {
        $data = [
            'age' => $this->faker->randomDigit,
            'name' => $this->faker->name
        ];

        $dto = new DummyDto($data);

        unset($dto->name);

        $debugInformation = $dto->__debugInfo();

        Debug::debug($debugInformation);

        $keys = array_keys($debugInformation);

        $this->assertNotContains('name', $keys);
    }
}

/**
 * Class Dummy Dto
 *
 * A dummy class, that extends the Data Transfer Object abstraction
 *
 * @property string $name
 * @property int $age
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class DummyDto extends DataTransferObject
{

    protected $name = '';

    protected $age = 0;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

}