<?php
use Aedart\DTO\DataTransferObject;

/**
 * Class Notes
 *
 * FOR TESTING ONLY
 *
 * @property string[] $notes
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class Notes extends DataTransferObject implements NotesInterface
{

    /**
     * @var string[]
     */
    protected $notes = [];

    /**
     * @param string[] $notes
     */
    public function setNotes(array $notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return string[]
     */
    public function getNotes()
    {
        return $this->notes;
    }
}