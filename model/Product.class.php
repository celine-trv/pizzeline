<?php

class Product
{
    // exactly same names as the fields in db
    private $id;
    private $name;
    private $category;
    private $ingredients;
    private $price_ht;
    private $tva;
    private $price_ttc;
    private $menu_number;
    private $is_vegetarian;
    private $is_orderable;


    /**
     * ***** CONSTRUCTOR *****
     *
     * @param   array       :   with data corresponding to properties of object (have to have field name in key : field => value)
     */
    public function __construct($data = []) {
        try {
            // set default values (if other values, they will be overwritten)
            $this->setMenu_number(NULL);
            $this->setIs_orderable(1);
    
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
        else throw new Exception("Product id has to be a positive number");
    }

    public function getId() {
        return $this->id;
    }
    

    // name
    public function setName($name) {
        $name = trim($name);

        if(is_string($name) && iconv_strlen($name) >= 2 && iconv_strlen($name) <= 50) {
            $array_name = explode(" ", $name);

            foreach($array_name as $key => $value) {
                $array_name[$key] = iconv_strlen($value) > 3 ? ucwords(mb_strtolower($value)) : mb_strtolower($value);
            }
            $this->name = implode(" ", $array_name);
        }
        else throw new Exception("Name length has to be between 2 and 50");
    }

    public function getName() {
        return $this->name;
    }
    

    // category
    public function setCategory($category) {
        $category = trim($category);

        if(is_string($category) && ($category == "entrees" || $category == "pizzas" || $category == "pates" || $category == "salades" || $category == "desserts" || $category == "boissons")) {
            $this->category = mb_strtolower($category);
        }
        else throw new Exception("Invalid category");
    }

    public function getCategory() {
        return $this->category;
    }
    

    // ingredients
    public function setIngredients($ingredients) {
        $ingredients = trim($ingredients);

        // $ingredients = str_replace([" ", ".", ",", "-", "/", ";"], ", ", $ingredients);

        if(is_string($ingredients) && iconv_strlen($ingredients) >= 2 && iconv_strlen($ingredients) <= 250) {
            $this->ingredients = mb_strtolower($ingredients);
        }
        else throw new Exception("Ingredients length has to be less than 250");
    }

    public function getIngredients() {
        return $this->ingredients;
    }
    

    // price_ht
    public function setPrice_ht($price_ht) {
        $price_ht = trim($price_ht);

        $price_ht = str_replace(",", ".", $price_ht);
        $price_ht = number_format($price_ht, 2, ".", "");

        if(is_numeric($price_ht) && iconv_strlen($price_ht) >= 4 && iconv_strlen(str_replace(".", "", $price_ht)) <= 6 && $price_ht > 0) {
            $this->price_ht = $price_ht;
        }
        else throw new Exception("price_ht has to be a number between 0.01 and 9999.99");
    }

    public function getPrice_ht() {
        return $this->price_ht;
    }
    

    // tva
    public function setTva($tva) {
        $tva = trim($tva);

        $tva = str_replace(",", ".", $tva);
        $tva = number_format($tva, 3, ".", "");

        if(is_numeric($tva) && iconv_strlen(str_replace(".", "", $tva)) == 4 && $tva > 0 && $tva < 1) {
            $this->tva = $tva;
        }
        else throw new Exception("tva has to be a number between 0.001 and 0.999");
    }

    public function getTva() {
        return $this->tva;
    }
    

    // price_ttc
    public function setPrice_ttc($price_ttc) {
        $price_ttc = trim($price_ttc);

        $price_ttc = str_replace(",", ".", $price_ttc);
        $price_ttc = number_format($price_ttc, 2, ".", "");

        if(is_numeric($price_ttc) && iconv_strlen($price_ttc) >= 4 && iconv_strlen(str_replace(".", "", $price_ttc)) <= 6 && $price_ttc > 0) {
            $this->price_ttc = $price_ttc;
        }
        else throw new Exception("price_ttc has to be a number between 0.01 and 9999.99");
    }

    public function getPrice_ttc() {
        return $this->price_ttc;
    }


    // menu_number
    public function setMenu_number($menu_number) {
        if(empty(trim($menu_number)) || $menu_number == NULL)
            $this->menu_number = NULL;
        else {
            $menu_number = trim($menu_number);

            if(is_numeric($menu_number) && !is_float($menu_number) && iconv_strlen($menu_number) <= 2) {
                $this->menu_number = (int) $menu_number;
            }
            else throw new Exception("menu number has to be an integer between 1 and 99");
        }
    }

    public function getMenu_number() {
        return $this->menu_number;
    }
    

    // is_vegetarian
    public function setIs_vegetarian($is_vegetarian) {
        $is_vegetarian = trim($is_vegetarian);

        if(is_numeric($is_vegetarian) && ($is_vegetarian == 0 || $is_vegetarian == 1) && iconv_strlen($is_vegetarian) === 1) {
            $this->is_vegetarian = (int) $is_vegetarian;
        }
        else throw new Exception("is_vegetarian has to be 0 or 1");
    }

    public function getIs_vegetarian() {
        return $this->is_vegetarian;
    }
    

    // is_orderable
    public function setIs_orderable($is_orderable) {
        $is_orderable = trim($is_orderable);

        if(is_numeric($is_orderable) && ($is_orderable == 0 || $is_orderable == 1) && iconv_strlen($is_orderable) === 1) {
            $this->is_orderable = (int) $is_orderable;
        }
        else throw new Exception("is_orderable has to be 0 or 1");
    }

    public function getIs_orderable() {
        return $this->is_orderable;
    }
}