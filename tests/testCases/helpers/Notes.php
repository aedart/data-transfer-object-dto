<?php
declare(strict_types=1);

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
    public function setNotes(array $notes) : void
    {
        $this->notes = $notes;
    }

    /**
     * @return string[]
     */
    public function getNotes() : array
    {
        return $this->notes;
    }
}