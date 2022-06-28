<?php
require_once("Manager.trait.php");

class OrdersManager
{
    use Manager;

    const TABLE = "orders";

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