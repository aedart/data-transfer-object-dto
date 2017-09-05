<?php
declare(strict_types=1);

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
    /**
     * @var string
     */
    protected $street = '';

    /**
     * @var City
     */
    protected $city = null;

    /**
     * @return string
     */
    public function getStreet() : ?string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(?string $street)
    {
        $this->street = $street;
    }

    /**
     * @return City
     */
    public function getCity() : ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     */
    public function setCity(?City $city)
    {
        $this->city = $city;
    }

}