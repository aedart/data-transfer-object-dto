<?php
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
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return NotesInterface
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param NotesInterface $notes
     */
    public function setNotes(NotesInterface $notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return BadUnpopulatableObject
     */
    public function getBadInstance()
    {
        return $this->badInstance;
    }

    /**
     * @param BadUnpopulatableObject $badInstance
     */
    public function setBadInstance(BadUnpopulatableObject $badInstance)
    {
        $this->badInstance = $badInstance;
    }
}