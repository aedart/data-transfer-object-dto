<?php
use Aedart\DTO\DataTransferObject;

/**
 * Class City (Dummy Dto)
 *
 * FOR TESTING ONLY
 *
 * @property string $name
 * @property int $zipCode
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class City extends DataTransferObject
{

    protected $name = '';

    protected $zipCode = 0;

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
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param int $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

}