<?php
use Aedart\DTO\DataTransferObject;

/**
 * Class Address (Dummy Dto)
 *
 * FOR TESTING ONLY
 *
 * @property string $street
 * @property City $city
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class Address extends DataTransferObject
{

    protected $street = '';

    /**
     * @var City
     */
    protected $city = null;

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;
    }

}