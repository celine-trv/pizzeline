<?php
require_once("CommonController.class.php");

class FrontController extends CommonController
{
    private $productManager;
    private $ordersManager;
    const MAX_PRODUCT_QTY = 20;
    const MAX_ORDER_QTY = 60;
    // just change these values here, it's enough / sufficient for everywhere (there's dataset in html/js)

    public function __construct() {
        parent::__construct();
        $this->productManager = new ProductManager;
        $this->ordersManager = new OrdersManager;
	}


    /******************************* CONTACT *******************************/
    /**
     * ***** CHECKING methods CONTACT *****
     */
    private function checkNameContact($name) {
        if(empty($name) || !is_string($name) || !preg_match("#^[a-zA-Zàâäéèêëêîïôöûüùç '-]{2,30}$#", $name)) {
            $error = "Nom invalide (30 caractères max)";
        }
        else $error = "";

        return $error;
    }

    private function checkSubjectContact($subject) {
        if(empty($subject) || !is_string($subject) || iconv_strlen($subject) < 2 || iconv_strlen($subject) > 250) {
            $error = "Objet invalide (250 caractères max)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkEmailContact($email) {
        if(empty($email) || !is_string($email) || iconv_strlen($email) > 250 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email invalide";
        }
        else $error = "";

        return $error;
    }
    
    private function checkPhoneContact($phone) {
        $phone = str_replace([" ", ".", ",", "-"], "", $phone);

        if(empty($phone) || !preg_match("#^0[1-79]([ .,-]?[0-9]{2}){4}$#", $phone) || !is_numeric($phone) || iconv_strlen($phone) != 10) {
            // first number has to be 0, second between 1 and 7 or 9 (no 08), and 4 combinations of 2 numbers which could be separated by 0 or 1 (?) .,- or space
            $error = "Numéro invalide (10 chiffres)";
        }
        else $error = "";

        return $error;
    }

    private function checkMessageContact($message) {
        if(empty($message) || !is_string($message) || iconv_strlen($message) < 5 || iconv_strlen($message) > 1500) {
            $error = "Message invalide (1500 caractères max)";
        }
        else $error = "";

        return $error;
    }


    /**
     * ***** ACTION methods CONTACT *****
     */
    private function contact($name, $subject, $email, $phone, $message) {
        if($_POST && isset($_POST["action"]) && $_POST["action"] == "contact") {
            $errors = [ "name" => $this->checkNameContact($name), 
                        "subject" => $this->checkSubjectContact($subject),
                        "email" => $this->checkEmailContact($email),
                        "phone" => $this->checkPhoneContact($phone),
                        "message" => $this->checkMessageContact($message)
                        ];

            if(empty(array_filter($errors))) {
                // checking if input names in POST are those expected (as $errors above)
                $compare_post = array_diff_key($_POST, $errors);

                // unset keys not affected in the exchange with db (no field)
                if(array_key_exists("token", $compare_post)) unset($compare_post["token"]);
                if(array_key_exists("action", $compare_post)) unset($compare_post["action"]);

                if(sizeof($compare_post) === 0) {
                    $name = ucwords(mb_strtolower($name));
                    $email = mb_strtolower($email);
                    $phone = str_replace([" ", ".", ",", "-"], "", $phone);

                    // sending by mail
                    $body = "<html>
                                <head>
                                    <style>a{text-decoration:underline; color:initial;} .div_img_mail img{height:100px; width:auto; float:right; margin:1rem;} .message_mail{width:95%; margin:0 auto;}</style>
                                </head>
                                <body><br>
                                    <div class='div_img_mail'><img src='https://" . dirname($_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"]) . "/public/img/logo.png' alt='logo restaurant italien pizzeline'></div><br>
                                    MESSAGE RECU DE : <br><br>
                                    Nom :       <b>" . $name . "</b><br>
                                    Email :     <b><a href='mailto:" . $email . "' target='blank'>" . $email . " </a></b><br>
                                    Téléphone : <b><a href='tel:" . $phone . "'>" . $phone . " </a></b><br>
                                    Objet :     <b>" . $subject . "</b><br><br>
                                    <div class='message_mail'>" . nl2br($message) . "</div><br>
                                </body>
                            </html>";
                    $subject = "Nouveau contact - Objet : " . $subject;

                    if(Mailer::sendEmail(["Céline" => "contact@celine-dev-web.fr"], $subject, $body)) {
                        $errors = [];
                    }
                    else
                        $errors = ["error" => "Une erreur est survenue, merci de réessayer"];
                }
                else
                    $errors = ["error" => "Veuillez vérifier les champs saisis"];
            }

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else
                return "Votre message a bien été envoyé";
        }
    }

    
    /******************************* MENU / CARTE *******************************/
    /**
     * ***** CHECKING methods MENU / CARTE *****
     */
    private function checkQuantity($quantity) {
        if(empty($quantity) || !preg_match("#^[0-9]{1,2}$#", $quantity) || $quantity < 1 || $quantity > self::MAX_PRODUCT_QTY) {
            $error = "Quantité invalide (" . self::MAX_PRODUCT_QTY . " max, au-delà nous contacter)";
        }
        else $error = "";

        return $error;
    }



    /**
     * ***** ACTION methods MENU / CARTE *****
     */
    private function all_products() {
        try {
            $products = $this->productManager->selectAll("ORDER BY price_ttc, name");

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

    private function add_current_order($id, $quantity) {
        if($_POST && isset($_GET["add"]) && !empty($_GET["add"])) {
            $errors = [ "quantity" => $this->checkQuantity($quantity) ];

            if(empty(array_filter($errors))) {
                if($id && $id > 0) {
                // Case $current_order = array("id_product1" => [data_product],"id_product2" => [data_product]);
                    // actual total quantity (before adding)
                    $total_qty = $this->total_qty_current_order();

                    // cheking if product already in current order -> just change quantity by adding
                    if(Session::get($id, "current_order")) {
                        $product = Session::get($id, "current_order");

                        // firstly, checking max product quantity
                        if($product["quantity"] + $quantity > self::MAX_PRODUCT_QTY) {
                            $quantity = self::MAX_PRODUCT_QTY - $product["quantity"];
                        }

                        // secondly, checking max total quantity
                        if($total_qty + $quantity > self::MAX_ORDER_QTY) {
                            $quantity = self::MAX_ORDER_QTY - $total_qty;
                        }

                        if($quantity > 0) {
                            $product["quantity"] += $quantity;

                            Session::put($id, $product, "current_order");
                            $errors = [];
                        }
                        else
                            $errors = ["error" => "Ajout impossible : commande max atteinte"];
                    }
                    // else add new product to current order
                    else {
                        // checking max total quantity
                        if($total_qty + $quantity > self::MAX_ORDER_QTY) {
                            $quantity = self::MAX_ORDER_QTY - $total_qty;
                        }

                        if($quantity > 0) {
                            try {
                                $data = $this->productManager->selectById($id);
                                $product = new Product($data);

                                if($product->getIs_orderable() != 0) {
                                    $array_product = [  "id" => $id,
                                                        "name" => $product->getName(),
                                                        "category" => $product->getCategory(),
                                                        "ingredients" => $product->getIngredients(),
                                                        "quantity" => $quantity,
                                                        "price_ttc" => $product->getPrice_ttc(),
                                                        "is_vegetarian" => $product->getIs_vegetarian(),
                                                    ];

                                    Session::put($id, $array_product,"current_order");
                                    $errors = [];
                                }
                                else
                                    $errors = ["error" => "Produit non disponible"];
                            }
                            catch (Exception $e) {
                                echo "<strong>ERROR : </strong>" . $e->getMessage();
                            }
                        }
                        else
                            $errors = ["error" => "Ajout impossible : commande max atteinte"];
                    }

                // Case $current_order = array("id" => [],"name" => [], "category" => [], "quantity" => [], "price_ttc" => [], "is_vegetarian" => []));
                    // $current_order = is_array(Session::get("current_order")) ? Session::get("current_order") : array();
                    // $total_qty = array_sum($current_order["quantity"]);

                    // $product_key = isset($current_order["id"]) ? array_search($id, $current_order["id"]) : false;
                        
                    // if($product_key !== false) {
                    //     // firstly, checking max product quantity
                    //     if($current_order["quantity"][$product_key] + $quantity > self::MAX_PRODUCT_QTY) {
                    //         $quantity = self::MAX_PRODUCT_QTY - $current_order["quantity"][$product_key];
                    //     }

                    //     // secondly, checking max total quantity
                    //     if($total_qty + $quantity > self::MAX_ORDER_QTY) {
                    //         $quantity = self::MAX_ORDER_QTY - $total_qty;
                    //     }

                    //     if($quantity > 0) {
                    //         $current_order["quantity"][$product_key] += $quantity;
                            
                    //         Session::put("current_order", $current_order);
                    //         $errors = [];
                    //     }
                    //     else
                    //         $errors = ["error" => "Ajout impossible : commande max atteinte"];
                    // }
                    // else {
                    //     // checking max total quantity
                    //     if($total_qty + $quantity > self::MAX_ORDER_QTY) {
                    //         $quantity = self::MAX_ORDER_QTY - $total_qty;
                    //     }

                    //     if($quantity > 0) {
                    //         try {
                    //             $data = $this->productManager->selectById($id);
                    //             $product = new Product($data);

                    //             if($product->getIs_orderable() != 0) {
                    //                 $current_order["id"][] = $id;
                    //                 $current_order["name"][] = $product->getName();
                    //                 $current_order["category"][] = $product->getCategory();
                    //                 $current_order["quantity"][] = $quantity;
                    //                 $current_order["price_ttc"][] = $product->getPrice_ttc();
                    //                 $current_order["is_vegetarian"][] = $product->getIs_vegetarian();

                    //                 Session::put("current_order", $current_order);
                    //                 $errors = [];
                    //             }
                    //             else
                    //                 $errors = ["error" => "Produit non disponible"];
                    //         }
                    //         catch (Exception $e) {
                    //             echo "<strong>ERROR : </strong>" . $e->getMessage();
                    //         }
                    //     }
                    //     else
                    //         $errors = ["error" => "Ajout impossible : commande max atteinte"];
                    // }
                }
                else
                    $errors = ["error" => "Identificaton du produit à ajouter impossible"];
            }

            // alert if max value
            $alert = $total_qty + $quantity >= self::MAX_ORDER_QTY ? " (" . self::MAX_ORDER_QTY . " produits max)" : "";
            $alert = Session::get($id, "current_order") && Session::get($id, "current_order")["quantity"] >= self::MAX_PRODUCT_QTY ? " (" . self::MAX_PRODUCT_QTY . " max)" : $alert;

            if($errors != [] || sizeof($errors) > 0)
                echo json_encode($errors);
            elseif($quantity == 1)
                echo json_encode(["validate" => "✓ " . $quantity . " ajouté !" . $alert, "quantity" => $quantity]);
            else
                echo json_encode(["validate" => "✓ " . $quantity . " ajoutés !" . $alert, "quantity" => $quantity]);
        }
    }

    private function total_qty_current_order() {
        $current_order = is_array(Session::get("current_order")) ? Session::get("current_order") : array();
        $total_qty = 0;

        foreach($current_order as $key => $value) {
            $total_qty += $current_order[$key]["quantity"];
        }
        return $total_qty;
    }

    private function categories_current_order() {
        if(Session::get("current_order")) {
            $categories = $this->productManager->enum("category");

            foreach(Session::get("current_order") as $product) {
                $cat[] = $product["category"];
            }
            return (array_intersect($categories, $cat));
        }
    }

    private function edit_qty_current_order($id, $quantity) {
        if($_POST && isset($_POST["edit"]) && $_POST["edit"] == "quantity" && isset($_GET["edit"]) && !empty($_GET["edit"])) {
            $errors = [ "quantity" => $this->checkQuantity($quantity) ];

            if(empty(array_filter($errors))) {
                if($id && $id > 0) {
                    // actual total quantity (before editing)
                    $total_qty = $this->total_qty_current_order();
                    
                    if(Session::get($id, "current_order")) {
                        $product = Session::get($id, "current_order");

                        // checking max total quantity
                        if($total_qty - $product["quantity"] + $quantity > self::MAX_ORDER_QTY) {
                            $quantity = self::MAX_ORDER_QTY - ($total_qty - $product["quantity"]);
                        }

                        if($quantity > 0) {
                            $product["quantity"] = $quantity;
    
                            Session::put($id, $product, "current_order");
                            $errors = [];
                        }
                        else
                            $errors = ["error" => "Ajout impossible : commande max atteinte"];
                    }
                    else
                        $errors = ["error" => "Ce produit ne fait pas partie de votre commande"];
                }
                else
                    $errors = ["error" => "Identificaton du produit à ajouter impossible"];
            }

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else 
                header("location: commande");
        }
    }

    private function delete_product_current_order($id) {
        if($_POST && isset($_POST["delete"]) && $_POST["delete"] == "product" && isset($_GET["delete"]) && !empty($_GET["delete"])) {
            if($id && $id > 0) {
                if(Session::delete($id, "current_order")) {
                    $current_order = Session::get("current_order");

                    if(is_array($current_order) && sizeof($current_order) == 0) 
                        Session::delete("current_order");

                    $errors = [];
                }
                else
                    $errors = ["error" => "Ce produit ne fait pas partie de votre commande"];
            }
            else
                $errors = ["error" => "Identificaton du produit à supprimer impossible"];

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else 
                header("location: commande");
        }
    }

    private function empty_current_order() {
        if($_POST && isset($_POST["delete"]) && $_POST["delete"] == "current_order") {
            Session::delete("current_order");
            header("location: commande");
        }
    }

    private function validate_order() {
        if($_POST && isset($_POST["create"]) && $_POST["create"] == "order") {
            if(Session::get("user")) {
                // CODE
            }
        }
    }


    /**
     * ***** RENDER the templates to the router (index.php) *****
     */
    public function view_home($template) {
        return $template->render([
            // "x" => $x,
        ]);
    }

    public function view_menu_carte($template) {
        if(isset($_GET["add"]) && !empty($_GET["add"])) {
            return $this->add_current_order(filter_input(INPUT_GET, 'add'), filter_input(INPUT_POST, 'quantity'));
        }
        else {
            return $template->render([
                "max_product_qty" => self::MAX_PRODUCT_QTY,
                "max_order_qty" => self::MAX_ORDER_QTY,
                "enum_category" => $this->productManager->enum("category"),
                "products" => $this->all_products(),
            ]);
        }
    }

    public function view_order($template) {
        return $template->render([
            "max_product_qty" => self::MAX_PRODUCT_QTY,
            "max_order_qty" => self::MAX_ORDER_QTY,
            "categories" => $this->categories_current_order(),
            "msg_edit_qty" => isset($_POST["edit"]) && $_POST["edit"] == "quantity" && isset($_GET["edit"]) && !empty($_GET["edit"]) ? $this->edit_qty_current_order(filter_input(INPUT_GET, 'edit'), filter_input(INPUT_POST, 'quantity')) : null,
            "msg_delete_product" => isset($_POST["delete"]) && $_POST["delete"] == "product" && isset($_GET["delete"]) && !empty($_GET["delete"]) ? $this->delete_product_current_order(filter_input(INPUT_GET, 'delete')) : null,
            "total_qty" => $this->total_qty_current_order(),
            "msg_empty_current_order" => isset($_POST["delete"]) && $_POST["delete"] == "current_order" ? $this->empty_current_order() : null,
            "enum_type" => $this->ordersManager->enum("type"),
            // "msg_validate_order" => ,
        ]);
    }

    public function view_booking($template) {
        return $template->render([
            // "x" => $x,
        ]);
    }

    public function view_infos($template) {
        return $template->render([
            // "x" => $x,
        ]);
    }
    
    public function view_reviews($template) {
        return $template->render([
            // "x" => $x,
        ]);
    }
    
    public function view_contact($template) {
        return $template->render([
            "msg_contact" => $this->contact(filter_input(INPUT_POST, 'name'), filter_input(INPUT_POST, 'subject'), filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'phone'), filter_input(INPUT_POST, 'message')),
        ]);
    }
}