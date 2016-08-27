<?php

/**
 * Interface NotesInterface
 *
 * FOR TESTING ONLY
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
interface NotesInterface
{

    /**
     * @param string[] $notes
     */
    public function setNotes(array $notes);

    /**
     * @return string[]
     */
    public function getNotes();
}