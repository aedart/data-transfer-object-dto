<?php
declare(strict_types=1);

use Aedart\DTO\DataTransferObject;

/**
 * Class Person (Dummy Dto)
 *
 * FOR TESTING ONLY
 *
 * @property string $name
 * @property Address $address
 * @property NotesInterface $notes
 *
 * @property BadUnpopulatableObject $badInstance
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class Person extends DataTransferObject
{

    protected $name = '';

    /**
     * @var Address
     */
    protected $address = null;

    /**
     * @var NotesInterface
     */
    protected $notes = null;

    /**
     * @var BadUnpopulatableObject
     */
    protected $badInstance = null;

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
     * @return Address|null
     */
    public function getAddress() : ?Address
    {
        return $this->address;
    }

    /**
     * @param Address|null $address
     */
    public function setAddress(?Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return NotesInterface|null
     */
    public function getNotes() : ?NotesInterface
    {
        return $this->notes;
    }

    /**
     * @param NotesInterface|null $notes
     */
    public function setNotes(?NotesInterface $notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return BadUnpopulatableObject|null
     */
    public function getBadInstance() : ?BadUnpopulatableObject
    {
        return $this->badInstance;
    }

    /**
     * @param BadUnpopulatableObject|null $badInstance
     */
    public function setBadInstance(?BadUnpopulatableObject $badInstance)
    {
        $this->badInstance = $badInstance;
    }
}