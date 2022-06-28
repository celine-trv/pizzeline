<?php

trait Manager 
{
    protected static $db;
    protected static $table;
    // const TABLE_NAME = "";
    // const TABLE = "get_called_class()::TABLE_NAME";


    /**
     * ***** DATABASE CONNEXION *****
     * 
     * @return <object>     :   PDO
     */
    protected static function getDb() {
        if(!static::$db) {
            try {
                $db_config = array();
                $db_config["SGBD"]      = "mysql";
                $db_config["HOST"]      = "localhost";
                $db_config["DB_NAME"]   = "pizzeline";
                $db_config["USERNAME"]  = "root";
                $db_config["PASSWORD"]  = "";
                $db_config["OPTIONS"]   = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
                
                static::$db = new PDO($db_config["SGBD"] .":host=". $db_config["HOST"] .";dbname=". $db_config["DB_NAME"] . ";charset=utf8",
                                    $db_config["USERNAME"],
                                    $db_config["PASSWORD"],
                                    $db_config["OPTIONS"]);
            }
            catch(PDOException $e) {
                echo "<strong>ERROR : </strong>" . $e->getMessage();
            }
            unset($db_config);
        }
        return static::$db;
    }

    // to control table name exists in db
    protected static function setTable($table_name) {
        $stmt = static::getDb()->prepare("SHOW TABLES");

        if($stmt->execute() === true) {
            $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            foreach($tables as $value) {
                foreach($value as $v) {
                    if($table_name == $v) {
                        static::$table = $table_name;
                    }
                }
            }
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
    }
    // public static function getTable() {
    //     return static::$table;
    // }



    // ***** GENERAL REQUESTS *****
    // improvement to think about : implode(" AND ", $array = [$key => "$field = '$value'"])

    /**
     * method to get columnsDescription of table
     * with sql prepared
     * 
     * @return  <array> :   with all characteristics of columns
     */
    public function columnsDesc() {
        $stmt = static::getDb()->prepare("DESC " . static::$table);

        if($stmt->execute() === true) {
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $columnsDesc [] = array_splice($r,1);
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
    }
    
    /**
     * method to get fields of table without the first (id) after columnsDesc()
     * with sql prepared
     * 
     * @param   array   $columnsDesc:   the return of columnsDesc() which has to be executed before
     * @return  <array>             :   with the fields names in key and default db values (in order to array_diff_key($_POST, $fields) or array_replace($fields, $_POST) after)
     */
    public function allFields($columnsDesc) {
        $fields = [];
        for($i = 0; $i < sizeof($columnsDesc); $i++) {
            if($columnsDesc[$i]["Null"] == "YES")     $fields[$columnsDesc[$i]["Field"]] = "NULL";
            elseif($columnsDesc[$i]["Field"] != "id") $fields[$columnsDesc[$i]["Field"]] = $columnsDesc[$i]["Default"];
        }
        return $fields;
    }

    /**
     * method to get an enum list sql into table
     * with sql prepared
     * 
     * @param   string   $field     :   the name of field with enum
     * @return  <array>             :   with the possible values of enum field / false if error
     */
	public function enum($field) {
        $stmt = static::getDb()->prepare('SHOW COLUMNS FROM `'. static::$table .'` LIKE "' . $field . '"');
        
        if($stmt->execute() === true) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            preg_match("/enum\(\'(.*)\'\)$/", $result['Type'], $matches);
    
            return explode('\',\'', $matches[1]);
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
	}

    /**
     * method to select all from table
     * with sql prepared
     * 
     * @param   string  $option :   to add ORDER BY or LIMIT condition for exemple (without $var !)
     * @return  <array>         :   with result(s) of request (fetch if one else fetchAll) / 0 if no result / false if error
     */
    public function selectAll($option = "") {
        $stmt = static::getDb()->prepare("SELECT * FROM " . static::$table . " $option");

        if($stmt->execute() === true) {
            if($stmt->rowCount() >= 1) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                return (int) 0;
            }
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
    }

    /**
     * method to select one row by id from table
     * with sql prepared
     * 
     * @param   int $id     :   id for WHERE condition
     * @return  <array>     :   with result(s) of request (fetch if one else fetchAll) / 0 if no result / false if error
     */
    public function selectById($id) {
        $stmt = static::getDb()->prepare("SELECT * FROM " . static::$table . " WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        if($stmt->execute() === true) {
            if($stmt->rowCount() == 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                return (int) 0;
            }
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
    }

    /**
     * method to select by a field from table
     * with sql prepared
     * 
     * @param   string      $field_where    :   name of the field in WHERE condition
     * @param   string-int  $value_where    :   value of the field in WHERE condition
     * @param   string      $option         :   to add ORDER BY or LIMIT condition for exemple (without $var !)
     * @return  <array>                     :   with result(s) of request (fetch if one else fetchAll) / 0 if no result / false if error
     */
    public function selectBy($field_where, $value_where, $option = "") {
        $stmt = static::getDb()->prepare("SELECT * FROM " . static::$table . " WHERE " . $field_where . " = :" . $field_where . " $option");

        if(is_int($value_where) || ctype_digit($value_where)) $pdo_param = PDO::PARAM_INT;
        else $pdo_param = PDO::PARAM_STR;

        $stmt->bindValue(":" . $field_where, $value_where, $pdo_param);

        if($stmt->execute() === true) {
            if($stmt->rowCount() == 1) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            elseif($stmt->rowCount() > 1) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                return (int) 0;
            }
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
    }

    /**
     * method to check the uniqueness of an element
     * with sql prepared
     * 
     * @param   string      $field_where    :   name of the field in WHERE condition
     * @param   string-int  $value_where    :   value of the field in WHERE condition
     * @return  <bool>                      :   true if unique / false if not
     */
    public function isUnique($field_where, $value_where) {
        try {
            if($this->selectBy($field_where, $value_where) === 0) {
                return true;
            }
            else return false;
        }
        catch(Exception $e) {
            throw new Exception("Impossible de vérifier l'unicité de l'élément : " . $e->getMessage());
        }
    }

    /**
     * method to insert a row into table
     * with sql prepared
     * 
     * @param   array   $data   :   array with all data to insert (have to have field name in key : field => value)
     * @return  <int>           :   id of inserted row / false if error
     */
    public function insert($data = []) {
        $stmt = static::getDb()->prepare('INSERT INTO ' . static::$table . ' (' . implode(', ', array_keys($data)) . ') VALUES (:' . implode(", :", array_keys($data)) . ')');

        foreach($data as $key => $value) {
            if(is_int($value) || ctype_digit($value)) $pdo_param = PDO::PARAM_INT;
            else $pdo_param = PDO::PARAM_STR;
            
            $stmt->bindValue(":" . $key, $value, $pdo_param);
        }

        if($stmt->execute() === true) {
		    return static::getDb()->lastInsertId();
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
    }

    /**
     * method to update a row into table
     * with sql prepared
     * 
     * @param   array       $data           :   array with all data to insert (have to have field name in key : field => value)
     * @param   string      $field_where    :   name of the field in WHERE condition
     * @param   string-int  $value_where    :   value to update in WHERE condition
     * @return  <int>                       :   number of rows affected / false if error
     */
	public function update($data = [], $field_where, $value_where) {
        foreach($data as $key => $value) { 
            $fields [] = $key . " = :" . $key;
        }
        $stmt = static::getDb()->prepare('UPDATE ' . static::$table . ' SET ' . implode(", ", $fields) . " WHERE " . $field_where . " = :" . $field_where);

        foreach($data as $key => $value) {
            if(is_int($value) || ctype_digit($value)) $pdo_param = PDO::PARAM_INT;
            else $pdo_param = PDO::PARAM_STR;
            
            $stmt->bindValue(":" . $key, $value, $pdo_param);
        }

        if(is_int($value_where) || ctype_digit($value_where)) $pdo_param = PDO::PARAM_INT;
        else $pdo_param = PDO::PARAM_STR;
        $stmt->bindValue(":" . $field_where, $value_where, $pdo_param);

        if($stmt->execute() === true) {
		    return $stmt->rowCount();
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
	}

    /**
     * method to delete row into table
     * with sql prepared
     * 
     * @param   string      $field_where    :   name of the field in WHERE condition
     * @param   string-int  $value_where    :   value of the field in WHERE condition
     * @return  <array>                     :   with infos of row(s) deleted (for custom message) / false if error
     */
	public function delete($field_where, $value_where) {
        $deleted = $this->selectBy($field_where, $value_where);

		$stmt = static::getDb()->prepare("DELETE FROM " . static::$table . " WHERE " . $field_where . " = :" . $field_where);

        if(is_int($value_where) || ctype_digit($value_where)) $pdo_param = PDO::PARAM_INT;
        else $pdo_param = PDO::PARAM_STR;
        $stmt->bindValue(":" . $field_where, $value_where, $pdo_param);

        if($stmt->execute() === true) {
		    return $deleted;
        }
        else {
            echo "<pre><strong>ERREUR : </strong><br>"; print_r($stmt->errorInfo() . "<br>" . __FUNCTION__ . " : " . __LINE__); echo "</pre>";
            return false;
        }
	}
}
