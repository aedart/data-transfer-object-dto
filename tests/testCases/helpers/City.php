<?php
declare(strict_types=1);

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
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getZipCode() : int
    {
        return $this->zipCode;
    }

    /**
     * @param int $zipCode
     */
    public function setZipCode(int $zipCode)
    {
        $this->zipCode = $zipCode;
    }

}