<?php
declare(strict_types=1);

/**
 * Class BadUnpopulatableObject
 *
 * FOR TESTING ONLY
 *
 * WARNING: This class does not inherit from populatable,
 * and therefore the DTO should fail, when attempting to
 * populate it!
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 */
class BadUnpopulatableObject
{

    /**
     * @var string
     */
    protected $foo = '';

    /**
     * @return string
     */
    public function getFoo() : string
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     */
    public function setFoo(string $foo)
    {
        $this->foo = $foo;
    }
}