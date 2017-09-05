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
     *
     * @return void
     */
    public function setNotes(array $notes) : void;

    /**
     * @return string[]
     */
    public function getNotes() : array;
}