<?php
require_once("Manager.trait.php");

class UserManager
{
    use Manager;

    const TABLE = "user";

    /**
     * CONSTRUCTOR
     *
     */
    public function __construct() {
        self::setTable(self::TABLE);
	}
    
    /**
     * FUNCTIONS
     */
    
}
