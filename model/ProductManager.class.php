<?php
require_once("Manager.trait.php");

class ProductManager
{
    use Manager;

    const TABLE = "product";

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