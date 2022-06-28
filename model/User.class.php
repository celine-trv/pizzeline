<?php

class User
{
    // exactly same names as the fields in db
    private $id;
    private $first_name;
    private $last_name;
    private $email;
    private $password;
    private $phone;
    private $address;
    private $zipcode;
    private $town;
    private $fidelity;
    private $status;


    /**
     * ***** CONSTRUCTOR *****
     * 
     * @param   array       :   with data corresponding to properties of object (have to have field name in key : field => value)
     */
    public function __construct($data = []) {
        try {
            // set default values (if other values, they will be overwritten)
            $this->setFidelity(0);
            $this->setStatus("utilisateur");
    
            // then hydrate with the data
            $this->hydrate($data);
        }
        catch (Exception $e) {
            echo "<strong>ERROR : </strong>" . $e->getMessage();
        }
	}


    /**
     * method to HYDRATE the object and the same for getter
     * 
     * @param   array       :   with data corresponding to properties of object (have to have field name in key : field => value)
     * @return  <mixed>     :   setters values
     */
    public function hydrate($data = []) {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            
            if(method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    public function getters($data = []) {
        foreach ($data as $key => $value) {
            $method = 'get' . ucfirst($key);
            if(method_exists($this, $method)) {
                $data[$key] = $this->$method();
            }
        }
        return $data;
    }


    /**
     * ***** GETTERS + SETTERS *****
     */
    // id
    public function setId($id) {
        $id = trim($id);

        if($id > 0 && is_numeric($id) && !is_float($id)) {
            $this->id = (int) $id;
        }
        else throw new Exception("User id has to be a positive number");
    }

    public function getId() {
        return $this->id;
    }
    

    // first_name
    public function setFirst_name($first_name) {
        $first_name = trim($first_name);

        if(is_string($first_name) && iconv_strlen($first_name) >= 2 && iconv_strlen($first_name) <= 30) {
            $this->first_name = ucwords(mb_strtolower($first_name));
        }
        else throw new Exception("Firstname length has to be between 2 and 30");
    }

    public function getFirst_name() {
        return $this->first_name;
    }
    

    // last_name
    public function setLast_name($last_name) {
        $last_name = trim($last_name);

        if(is_string($last_name) && iconv_strlen($last_name) >= 2 && iconv_strlen($last_name) <= 30) {
            $this->last_name = mb_strtoupper($last_name);
        }
        else throw new Exception("Lastname length has to be between 2 and 30");
    }

    public function getLast_name() {
        return $this->last_name;
    }
    

    // email
    public function setEmail($email) {
        $email = trim($email);

        if(is_string($email) && iconv_strlen($email) <= 250 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = mb_strtolower($email);
        }
        else throw new Exception("Invalid email");
    }

    public function getEmail() {
        return $this->email;
    }
    

    // password
    public function setPassword($password) {
        $password = trim($password);

        if(is_string($password) && iconv_strlen($password) <= 250) {
            $this->password = $password;
        }
        else throw new Exception("Hashed password length has to be less than 250");
    }

    public function getPassword() {
        return $this->password;
    }
    

    // phone
    public function setPhone($phone) {
        $phone = trim($phone);

        $phone = str_replace([" ", ".", ",", "-"], "", $phone);

        if(is_numeric($phone) && iconv_strlen($phone) == 10) {
            $this->phone = $phone;
        }
        else throw new Exception("Phone number length has to be 10 numbers");
    }

    public function getPhone() {
        return $this->phone;
    }
    

    // address
    public function setAddress($address) {
        $address = trim($address);

        if(is_string($address) && iconv_strlen($address) >= 5 && iconv_strlen($address) <= 250) {
            $this->address = ucwords(mb_strtolower($address));
        }
        else throw new Exception("Address length has to be between 5 and 250");
    }

    public function getAddress() {
        return $this->address;
    }
    

    // zipcode
    public function setZipcode($zipcode) {
        $zipcode = trim($zipcode);

        $zipcode = str_replace([" ", ".", ","], "", $zipcode);

        if(is_numeric($zipcode) && iconv_strlen($zipcode) == 5) {
            $this->zipcode = $zipcode;
        }
        else throw new Exception("Zipcode length has to be 5 numbers");
    }

    public function getZipcode() {
        return $this->zipcode;
    }

    
    // town
    public function setTown($town) {
        $town = trim($town);

        if(is_string($town) && iconv_strlen($town) >= 2 && iconv_strlen($town) <= 50) {
            $this->town = ucwords(mb_strtolower($town));
        }
        else throw new Exception("Town name length has to be between 2 and 50");
    }

    public function getTown() {
        return $this->town;
    }
    

    // fidelity
    public function setFidelity($fidelity) {
        $fidelity = trim($fidelity);

        $fidelity = str_replace(" ", "", $fidelity);

        if(is_numeric($fidelity) && !is_float($fidelity) && $fidelity >= 0 && iconv_strlen($fidelity) <= 4) {
            $this->fidelity = (int) $fidelity;
        }
        else throw new Exception("Fidelity has to be a number between 0 and 9999");
    }

    public function getFidelity() {
        return $this->fidelity;
    }
    

    // status
    public function setStatus($status) {
        $status = trim($status);

        if(is_string($status) && ($status == "utilisateur" || $status == "admin")) {
            $this->status = $status;
        }
        else throw new Exception("Invalid status");
    }

    public function getStatus() {
        return $this->status;
    }
}
