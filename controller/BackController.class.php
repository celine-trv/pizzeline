<?php
require_once("CommonController.class.php");

class BackController extends CommonController
{
    private $userManager;
    private $productManager;

    public function __construct() {
        parent::__construct();
        $this->userManager = new UserManager;
        $this->productManager = new ProductManager;
	}


    /******************************* ADMIN USERS *******************************/
    /**
     * ***** CHECKING methods USER *****
     */
    private function checkFidelity($fidelity) {
        if(empty($fidelity) && iconv_strlen($fidelity) <= 0 || !preg_match("#^[0-9]{1,4}$#", $fidelity)) {
            $error = "Points fidélité invalides (9999 max)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkStatus($status) {
        if(empty($status) || !in_array($status, $this->userManager->enum("status"))) {
            $error = "Statut invalide";
        }
        else $error = "";

        return $error;
    }


    /**
     * ***** ACTION methods USER *****
     */
    private function all_users() {
        try {
            $users = $this->userManager->selectAll("ORDER BY status, last_name");

            if(is_array($users)) {
                return $users;
            }
            elseif(!$users)
                return "Aucun utilisateur trouvé";
        }
        catch (Exception $e) {
            echo "<strong>ERROR : </strong>" . $e->getMessage();
        }
    }

    private function admin_edit_user($id, $fidelity, $status) {
        if($_POST && isset($_GET["edit"]) && !empty($_GET["edit"])) {
            $errors = [ "fidelity" => $this->checkFidelity($fidelity),
                        "status" => $this->checkStatus($status),
                      ];

            if(empty(array_filter($errors))) {
                if($id && $id > 0) {
                    try {
                        $data = $this->userManager->selectById($id);
                        $user = !empty($data) ? new User($data) : null;

                        if($user) {
                            $user->setFidelity($fidelity);
                            $user->setStatus($status);
                            $this->userManager->update(["fidelity" => $user->getFidelity(), "status" => $user->getStatus()], "id", $id);
                            $errors = [];
                        }
                        else
                            $errors = ["error" => "Utilisateur non reconnu, veuillez réessayer"];
                    }
                    catch (Exception $e) {
                        echo "<strong>ERROR : </strong>" . $e->getMessage();
                    }
                }
                else
                    $errors = ["error" => "Identificaton de l'utilisateur impossible"];
            }

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else 
                return "Les informations de " . $user->getLast_name() . " " . $user->getFirst_name() . " ont bien été mises à jour";
        }
    }

    private function admin_delete_user($id) {
        if(isset($_GET["delete"]) && !empty($_GET["delete"])) {
            if($id && $id > 0) {
                try {
                    $data = $this->userManager->selectById($id);
                    $user = !empty($data) ? new User($data) : null;

                    if($user) {
                        $this->userManager->delete("id", $id);
                        $errors = [];
                    }
                    else
                        $errors = ["error" => "Utilisateur non reconnu, veuillez réessayer"];
                }
                catch (Exception $e) {
                    echo "<strong>ERROR : </strong>" . $e->getMessage();
                }
            }
            else
                $errors = ["error" => "Identificaton de l'utilisateur impossible"];

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else 
                return "L'utilisateur " . $user->getLast_name() . " " . $user->getFirst_name() . " a bien été supprimé";
        }
    }


    /******************************* ADMIN PRODUCTS *******************************/
    /**
     * ***** CHECKING methods PRODUCT *****
     */
    private function checkName_product($name) {
        if(empty($name) || !preg_match("#^[a-zA-Z0-9àâäéèêëêîïôöûüùç ()'-/]{2,50}$#", $name)) {
            $error = "Nom invalide (50 caractères max)";
        }
        else $error = "";

        return $error;
    }

    private function checkIngredients($ingredients) {
        if(empty($ingredients) || !preg_match("#^[a-zA-Z0-9àâäéèêëêîïôöûüùç ()',-/]{2,250}$#", $ingredients)) {
            $error = "Liste d'ingrédients invalide (250 caractères max)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkCategory($category) {
        if(empty($category) || !in_array($category, $this->productManager->enum("category"))) {
            $error = "Categorie invalide";
        }
        else $error = "";

        return $error;
    }

    private function checkPrice_ht($price_ht, $tva, $price_ttc) {
        $price_ht = number_format($price_ht, 2, ".", "");

        if($this->checkPrice($price_ht) != "" || $price_ht != number_format(str_replace(",", ".", $price_ttc) / (1 + $tva), 2, ".", "")) {
            $error = "Erreur prix";
        }
        else $error = "";

        return $error;
    }

    private function checkTva($tva) {
        if(empty($tva) || !preg_match("#^0[.,][0-9]{1,3}$#", $tva) || $tva > 0.999) {
            $error = "Tva invalide (0.001 à 0.999)";
        }
        else $error = "";

        return $error;
    }

    private function checkPrice($price) {
        if(empty($price) || !preg_match("#^[1-9][0-9]{0,3}([.,][0-9]{1,2})?$#", $price) || $price > 9999.99) {
            $error = "Prix invalide (0.01 à 9999.99)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkMenu_number($menu_number) {
        if(!empty($menu_number) && !preg_match("#^[1-9][0-9]?$#", $menu_number) || $menu_number > 99) {
            $error = "Numéro de menu invalide";
        }
        else $error = "";

        return $error;
    }

    private function checkBool($bool) {
        if(!preg_match("#^[01]$#", $bool)) {
            $error = "Valeur invalide";
        }
        else $error = "";

        return $error;
    }


    /**
     * ***** ACTION methods PRODUCT *****
     */
    private function all_products() {
        try {
            $products = $this->productManager->selectAll("ORDER BY category, name");

            if(is_array($products)) {
                return $products;
            }
            elseif(!$products)
                return "Aucun produit trouvé";
        }
        catch (Exception $e) {
            echo "<strong>ERROR : </strong>" . $e->getMessage();
        }
    }

    private function admin_add_product($name, $category, $ingredients, $price_ht, $tva, $price_ttc, $menu_number, $is_vegetarian, $is_orderable) {
        if($_POST && isset($_POST["action"]) && $_POST["action"] == "add") {
            $errors = ["name" => $this->checkName_product($name),
                       "category" => $this->checkCategory($category),
                       "ingredients" => $this->checkIngredients($ingredients),
                       "price_ht" => $this->checkPrice_ht($price_ht, $tva, $price_ttc),
                       "tva" => $this->checkTva($tva),
                       "price_ttc" => $this->checkPrice($price_ttc),
                       "menu_number" => $this->checkMenu_number($menu_number),
                       "is_vegetarian" => $this->checkBool($is_vegetarian),
                       "is_orderable" => $this->checkBool($is_orderable),
                       ];

            if(empty(array_filter($errors))) {
                $data = ["name" => $name, 
                        "category" => $category,
                        "ingredients" => $ingredients,
                        "price_ht" => $price_ht,
                        "tva" => $tva,
                        "price_ttc" => $price_ttc,
                        "menu_number" => $menu_number,
                        "is_vegetarian" => $is_vegetarian,
                        "is_orderable" => $is_orderable
                        ];

                try {
                    $product = new Product($data);
                    $data = $product->getters($data);

                    $id = $this->productManager->insert($data);
                    $product->setId($id);
                    
                    $errors = [];
                }
                catch (Exception $e) {
                    echo "<strong>ERROR : </strong>" . $e->getMessage();
                }
            }

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else
                return 'Le produit "' . $product->getName() . '" a bien été ajouté';
        }
    }
    
    private function admin_edit_product($id, $name, $category, $ingredients, $price_ht, $tva, $price_ttc, $menu_number, $is_vegetarian, $is_orderable) {
        if($_POST && isset($_GET["edit"]) && !empty($_GET["edit"]) && $id > 0) {
            $errors = ["name" => $this->checkName_product($name),
                       "category" => $this->checkCategory($category),
                       "ingredients" => $this->checkIngredients($ingredients),
                       "price_ht" => $this->checkPrice_ht($price_ht, $tva, $price_ttc),
                       "tva" => $this->checkTva($tva),
                       "price_ttc" => $this->checkPrice($price_ttc),
                       "menu_number" => $this->checkMenu_number($menu_number),
                       "is_vegetarian" => $this->checkBool($is_vegetarian),
                       "is_orderable" => $this->checkBool($is_orderable),
                       ];

            if(empty(array_filter($errors))) {
                // checking diff between infos in db and infos in POST
                $data = $this->edited_product($id);

                $post = ["name" => $name, 
                        "category" => $category,
                        "ingredients" => $ingredients,
                        "price_ht" => $price_ht,
                        "tva" => $tva,
                        "price_ttc" => $price_ttc,
                        "menu_number" => $menu_number,
                        "is_vegetarian" => $is_vegetarian,
                        "is_orderable" => $is_orderable
                        ];

                if(is_array($data) && !array_key_exists("error", $data)) {
                    $new_data = array_diff_assoc($post, $data);

                    // if there are diff (changed) values -> update just these values
                    if(!empty($new_data)) {
                        try {
                            $product = !empty($data) ? new Product($data) : null;

                            if($product) {
                                $product->hydrate($new_data);
                                $changes = array_intersect_key($product->getters($new_data), $new_data);

                                $this->productManager->update($changes, "id", $id);

                                $errors = [];
                            }
                            else
                                $errors = ["error" => "Identificaton du produit à modifier impossible"];
                        }
                        catch (Exception $e) {
                            echo "<strong>ERROR : </strong>" . $e->getMessage();
                        }
                    }
                    elseif(empty($new_data)) {
                        $errors = ["error" => "Vous n'avez pas effectué de modification"];
                        header("location: admin-carte");
                    }
                }
                else
                    $errors = ["error" => "Identificaton du produit à modifier impossible"];
            }

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else {
                header("location: admin-carte");
                return 'Le produit "' . $product->getName() . '" a bien été modifié';
            }
        }
    }

    private function edited_product($id) {
        if(isset($_GET["edit"]) && !empty($_GET["edit"])) {
            if($id && $id > 0) {
                try {
                    $edited_product = $this->productManager->selectById($id);
                    $errors = [];
                }
                catch (Exception $e) {
                    echo "<strong>ERROR : </strong>" . $e->getMessage();
                }
            }
            else
                $errors = ["error" => "Identificaton du produit à modifier impossible"];
                
            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else 
                return $edited_product;
        }
    }

    private function admin_delete_product($id) {
        if($_POST && isset($_GET["delete"]) && !empty($_GET["delete"])) {
            if($id && $id > 0) {
                try {
                    $data = $this->productManager->selectById($id);
                    $product = !empty($data) ? new Product($data) : null;

                    if($product) {
                        $this->productManager->delete("id", $id);
                        $errors = [];
                    }
                    else
                        $errors = ["error" => "Produit non reconnu, veuillez réessayer"];
                }
                catch (Exception $e) {
                    echo "<strong>ERROR : </strong>" . $e->getMessage();
                }
            }
            else
                $errors = ["error" => "Identificaton du produit impossible"];

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else 
                return 'Le produit "' . $product->getName() . '" a bien été supprimé';
        }
    }


    /**
     * ***** RENDER the templates to the router (index.php) *****
     */
    public function view_admin_backoffice($template) {
        if(Session::get("user")["status"] != "admin") {
            header("location: " . dirname($_SERVER["SCRIPT_NAME"]));            // + Modal "Vous n'avez pas accès à ce lien" ??
        }
        else {
            return $template->render([
                // "x" => $x,
            ]);
        }
    }

    public function view_admin_orders($template) {
        if(Session::get("user")["status"] != "admin") {
            header("location: " . dirname($_SERVER["SCRIPT_NAME"]));
        }
        else {
            return $template->render([
                // "x" => $x,
            ]);
        }
    }
    
    public function view_admin_bookings($template) {
        if(Session::get("user")["status"] != "admin") {
            header("location: " . dirname($_SERVER["SCRIPT_NAME"]));
        }
        else {
            return $template->render([
                // "x" => $x,
            ]);
        }
    }
    
    public function view_admin_products($template) {
        if(Session::get("user")["status"] != "admin") {
            header("location: " . dirname($_SERVER["SCRIPT_NAME"]));
        }
        else {
            return $template->render([
                "edited_product" => $this->edited_product(filter_input(INPUT_GET, "edit")),
                "msg_admin_products" => 
                    isset($_POST["action"]) && !empty($_POST["action"]) && $_POST["action"] == "add" ? 
                        $this->admin_add_product(filter_input(INPUT_POST, "name"), filter_input(INPUT_POST, "category"), filter_input(INPUT_POST, "ingredients"), filter_input(INPUT_POST, "price_ht"), filter_input(INPUT_POST, "tva"), filter_input(INPUT_POST, "price_ttc"), filter_input(INPUT_POST, "menu_number"), filter_input(INPUT_POST, "is_vegetarian"), filter_input(INPUT_POST, "is_orderable")) : 

                    (isset($_GET["edit"]) && !empty($_GET["edit"]) ? 
                        $this->admin_edit_product(filter_input(INPUT_GET, "edit"), filter_input(INPUT_POST, "name"), filter_input(INPUT_POST, "category"), filter_input(INPUT_POST, "ingredients"), filter_input(INPUT_POST, "price_ht"), filter_input(INPUT_POST, "tva"), filter_input(INPUT_POST, "price_ttc"), filter_input(INPUT_POST, "menu_number"), filter_input(INPUT_POST, "is_vegetarian"), filter_input(INPUT_POST, "is_orderable")) : 

                    (isset($_GET["delete"]) && !empty($_GET["delete"]) ? 
                        $this->admin_delete_product(filter_input(INPUT_GET, "delete")) : null)),
                "enum_category" => $this->productManager->enum("category"),
                "products" => $this->all_products(),
            ]);
        }
    }
    
    public function view_admin_users($template) {
        if(Session::get("user")["status"] != "admin") {
            header("location: " . dirname($_SERVER["SCRIPT_NAME"]));
        }
        else {
            return $template->render([
                "msg_admin_users" => isset($_GET["edit"]) ? $this->admin_edit_user(filter_input(INPUT_GET, "edit"), filter_input(INPUT_POST, 'fidelity'), filter_input(INPUT_POST, 'status')) : (isset($_GET["delete"]) ? $this->admin_delete_user(filter_input(INPUT_GET, "delete")) : null),
                "enum_status" => $this->userManager->enum("status"),
                "users" => $this->all_users(),
            ]);
        }
    }
    
    public function view_admin_reviews($template) {
        if(Session::get("user")["status"] != "admin") {
            header("location: " . dirname($_SERVER["SCRIPT_NAME"]));
        }
        else {
            return $template->render([
                // "x" => $x,
            ]);
        }
    }
}