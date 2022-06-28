<?php
require_once("CommonController.class.php");

class SecurityController extends CommonController
{
    private $userManager;

    public function __construct() {
        parent::__construct();
        $this->userManager = new UserManager;
	}


    /**
     * ***** CHECKING methods *****
     */
    private function checkFirst_name($first_name) {
        if(empty($first_name) || !preg_match("#^[a-zA-Zàâäéèêëêîïôöûüùç '-]{2,30}$#", $first_name)) {
            $error = "Prénom invalide (30 caractères max)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkLast_name($last_name) {
        if(empty($last_name) || !preg_match("#^[a-zA-Zàâäéèêëêîïôöûüùç '-]{2,30}$#", $last_name)) {
            $error = "Nom invalide (30 caractères max)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkEmail($email) {
        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email invalide";
        }
        else $error = "";

        return $error;
    }
    
    private function checkEmail_isUnique($email) {
        if(!$this->userManager->isUnique("email", $email)) {
            $error = "Email déjà inscrit";
        }
        else $error = "";

        return $error;
    }
    
    private function checkPassword($password) {
        if(empty($password) || !preg_match("#^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[-+!$*_])([-+!$*_\w]{8,30})$#", $password)) {
            // (?=...)  -> condition : match everything enclosed (each one has to be valid)
            // .*       -> . for any one character (all) and * for 0 or more of previous character
            $error = "Avec majuscule, minuscule, chiffre et un symbole + - ! $ * _";
        }
        else $error = "";

        return $error;
    }
    
    private function checkConf_password($conf_password, $password) {
        if(empty($conf_password) || $conf_password != $password) {
            $error = "Mots de passe non identiques";
        }
        else $error = "";

        return $error;
    }
    
    private function checkPhone($phone) {
        if(empty($phone) || !preg_match("#^0[1-79]([ .,-]?[0-9]{2}){4}$#", $phone) || iconv_strlen(str_replace([" ", ".", ",", "-"], "", $phone)) != 10) {
            // first number has to be 0, second between 1 and 7 or 9 (no 08), and 4 combinations of 2 numbers which could be separated by 0 or 1 (?) .,- or space
            $error = "Numéro invalide (10 chiffres)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkAddress($address) {
        if(empty($address) || !preg_match("#^[a-zA-Z0-9àâäéèêëêîïôöûüùç ,'-]{5,250}$#", $address)) {
            $error = "Adresse invalide (250 caractères max)";
        }
        else $error = "";

        return $error;
    }
    
    private function checkZipcode($zipcode) {
        if(empty($zipcode) || !preg_match("#^(0[1-9]|[1-9][0-9])([0-9]{3})$#", $zipcode)) {
            $error = "Code postal invalide";
        }
        else $error = "";

        return $error;
    }
    
    private function checkTown($town) {
        if(empty($town) || !preg_match("#^[a-zA-Zàâäéèêëêîïôöûüùç /'-]{2,50}$#", $town)) {
            $error = "Ville invalide (50 caractères max)";
        }
        else $error = "";

        return $error;
    }


    /**
     * ***** ACTION methods *****
     */
    private function registration($first_name, $last_name, $email, $password, $conf_password, $phone, $address, $zipcode, $town) {
        if($_POST && isset($_POST["action"]) && $_POST["action"] == "registration") {
            $errors = [ "first_name" => $this->checkFirst_name($first_name), 
                        "last_name" => $this->checkLast_name($last_name),
                        "email" => empty($this->checkEmail($email)) ? $this->checkEmail_isUnique($email) : $this->checkEmail($email),
                        "password" => $this->checkPassword($password),
                        "conf_password" => $this->checkConf_password($conf_password, $password),
                        "phone" => $this->checkPhone($phone),
                        "address" => $this->checkAddress($address),
                        "zipcode" => $this->checkZipcode($zipcode),
                        "town" => $this->checkTown($town)
                        ];

            if(empty(array_filter($errors))) {
                $data = ["first_name" => $first_name, 
                        "last_name" => $last_name,
                        "email" => $email,
                        "password" => password_hash($password, PASSWORD_BCRYPT),
                        "phone" => $phone,
                        "address" => $address,
                        "zipcode" => $zipcode,
                        "town" => $town
                        ];

                try {
                    $user = new User($data);
                    $data = $user->getters($data);

                    $id = $this->userManager->insert($data);
                    $user->setId($id);
                
                    $array_user = $this->userManager->selectById($user->getId());
                    if(array_key_exists("password", $array_user)) unset($array_user["password"]);
                    
                    Session::delete("token");
                    Session::regenerate_id();
                    Session::generate_token();
                    Session::put("user", $array_user);

                    // email confirmation
                    $subject = "Bienvenue parmi nous " . $user->getFirst_name();
                    $body = "<html>
                                <head>
                                    <style>a{text-decoration:underline; color:#8c051e;} .div_img_mail img{height:100px; width:auto; float:right; margin:1rem;}</style>
                                </head>
                                <body><br>
                                    <div class='div_img_mail'><img src='https://" . dirname($_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"]) . "/public/img/logo.png' alt='logo restaurant italien pizzeline'></div>
                                    <div>
                                        <p>Félicitation " . $user->getFirst_name() . ", nous vous confirmons votre inscription sur le site Pizzeline.<br><br>Vous pouvez désormais passer votre première commande ou bien réserver une table pour déguster nos meilleures spécialités italiennes.<br><br>A bientôt !<br><br>
                                            <a href='https://" . dirname($_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"]) . "/carte' target='blank'>Je commande</a><br>
                                            <a href='https://" . dirname($_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"]) . "/reservation' target='blank'>Je réserve une table</a><br>
                                        </p>
                                    </div>
                                </body>
                            </html>";
                    Mailer::sendEmail([$user->getEmail()], $subject, $body, ["Céline" => "contact@celine-dev-web.fr"]);

                    $errors = [];
                }
                catch (Exception $e) {
                    echo "<strong>ERROR : </strong>" . $e->getMessage();
                }
            }

            if($errors != [] || sizeof($errors) > 0)
                return $errors;
            else {
                header("Refresh: 4; URL = mon-compte");
                return "Votre profil a bien été créé, vous allez être redirigé vers votre compte...";
            }
        }
    }

    private function login($email, $password) {
        if($_POST) {
            $errors = [ "email" => $this->checkEmail($email),
                        "password" => $this->checkPassword($password)
                      ];

            if(empty(array_filter($errors))) {
                try {
                    $data = $this->userManager->selectBy("email", $email);
                    $user = !empty($data) ? new User($data) : null;

                    if($user && password_verify($password, $user->getPassword())) {
                        $data = $user->getters($data);

                        if(array_key_exists("password", $data)) unset($data["password"]);

                        Session::delete("token");
                        Session::regenerate_id();
                        Session::generate_token();
                        Session::put("user", $data);

                        $errors = [];
                    }
                    else
                        $errors = ["error" => "Identifiant ou mot de passe incorrect"];
                }
                catch (Exception $e) {
                    echo "<strong>ERROR : </strong>" . $e->getMessage();
                }
            }
            echo json_encode($errors);
        }
    }

    private function edit_account($first_name, $last_name, $email, $phone, $address, $zipcode, $town) {
        if($_POST && isset($_GET["edit"]) && $_GET["edit"] == Session::get("id", "user")) {
            $errors = [ "first_name" => $this->checkFirst_name($first_name), 
                        "last_name" => $this->checkLast_name($last_name),
                        "email" => $this->checkEmail($email),
                        "phone" => $this->checkPhone($phone),
                        "address" => $this->checkAddress($address),
                        "zipcode" => $this->checkZipcode($zipcode),
                        "town" => $this->checkTown($town)
                        ];

            if(empty(array_filter($errors))) {
                // checking diff between infos in db of user connected and infos in POST
                $data = $this->userManager->selectById(Session::get("id", "user"));

                $post = ["first_name" => $first_name, 
                         "last_name" => $last_name,
                         "email" => $email,
                         "phone" => $phone,
                         "address" => $address,
                         "zipcode" => $zipcode,
                         "town" => $town
                        ];

                $new_data = array_diff_assoc($post, $data);

                // if there are diff (changed) values -> update just these values
                if(!empty($new_data)) {
                    $errors = [ "email" => array_key_exists("email", $new_data) ? $this->checkEmail_isUnique($email) : "" ];
                
                    if(empty(array_filter($errors))) {
                        try {
                            $user = !empty($data) ? new User($data) : null;

                            if($user) {
                                $user->hydrate($new_data);
                                $changes = array_intersect_key($user->getters($new_data), $new_data);

                                $this->userManager->update($changes, "id", Session::get("id", "user"));
                                
                                foreach($changes as $key => $value) {
                                    Session::put($key, $value, "user");
                                }

                                $errors = [];
                            }
                            else
                                $errors = ["error" => "Identification impossible, veuillez vous reconnecter"];
                        }
                        catch (Exception $e) {
                            echo "<strong>ERROR : </strong>" . $e->getMessage();
                        }
                    }
                }
                elseif(empty($new_data))
                    $errors = ["validate" => "Vous n'avez pas effectué de modification"];
            }

            if($errors != [] || sizeof($errors) > 0)
                echo json_encode($errors);
            else
                echo json_encode(["validate" => "Vos informations ont bien été modifiées"]);
        }
    }

    private function edit_password($old_password, $new_password, $conf_new_password) {
        if($_POST && isset($_GET["edit"]) && $_GET["edit"] == Session::get("id", "user")) {
            $errors = [ "old_password" => $this->checkPassword($old_password),
                        "new_password" => $this->checkPassword($new_password),
                        "conf_new_password" => $this->checkConf_password($conf_new_password, $new_password),
                        ];

            if(empty(array_filter($errors))) {
                try {
                    $data = $this->userManager->selectById(Session::get("id", "user"));
                    $user = !empty($data) ? new User($data) : null;

                    if($user) {
                        if(password_verify($old_password, $user->getPassword())) {
                            $password = ["password" => password_hash($new_password, PASSWORD_BCRYPT)];

                            $user->hydrate($password);
                            $change = $user->getters($password);
                            
                            $this->userManager->update($change, "id", Session::get("id", "user"));

                            $errors = [];
                        }
                        else
                            $errors = ["error" => "Veuillez vérifier l'ancien mot de passe"];
                    }
                    else
                        $errors = ["error" => "Identification impossible, veuillez vous reconnecter"];
                }
                catch (Exception $e) {
                    echo "<strong>ERROR : </strong>" . $e->getMessage();
                }
            }

            if($errors != [] || sizeof($errors) > 0)
                echo json_encode($errors);
                // return $errors;
            else
                echo json_encode(["validate" => "Votre mot de passe a bien été modifié"]);
                // header("Refresh: 4; URL = mon-compte");
                // return "Votre mot de passe a bien été modifié, vous allez être redirigé vers votre compte...";
        }
    }

    private function logout() {
        Session::delete("user");
        Session::delete("token");
        Session::delete("token_time");
        Session::delete("session_time");
        // Session::delete("others ???");
        // Session::destroy();         // both (unset & destroy) are complementary to delete all global variables + the session
        header("location: " . dirname($_SERVER["SCRIPT_NAME"]));
    }


    /**
     * ***** RENDER the templates to the router (index.php) *****
     */
    public function view_registration($template) {
        if(Session::get("user")) {
            header("location: mon-compte");
        }
        else {
            return $template->render([
                "msg_regist" => $this->registration(filter_input(INPUT_POST, 'first_name'), filter_input(INPUT_POST, 'last_name'), filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'password'), filter_input(INPUT_POST, 'conf_password'), filter_input(INPUT_POST, 'phone'), filter_input(INPUT_POST, 'address'), filter_input(INPUT_POST, 'zipcode'), filter_input(INPUT_POST, 'town')),
            ]);
        }
    }

    public function view_login() {
        return $this->login(filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'password'));
    }
    
    public function view_account($template) {
        if(!Session::get("user")) {
            $this->twig->display("front/home.html.twig", ["must_login" => true]);
        }
        elseif(isset($_GET["edit"]) && $_GET["edit"] == Session::get("id", "user")) {
            return $this->edit_account(filter_input(INPUT_POST, 'first_name'), filter_input(INPUT_POST, 'last_name'), filter_input(INPUT_POST, 'email'), filter_input(INPUT_POST, 'phone'), filter_input(INPUT_POST, 'address'), filter_input(INPUT_POST, 'zipcode'), filter_input(INPUT_POST, 'town'));
        }
        else {
            return $template->render([
                "display_session_phone" => wordwrap(Session::get("user")["phone"], 2, " ", true),
            ]);
        }
    }
    
    public function view_password($template) {
        if(!Session::get("user")) {
            $this->twig->display("front/home.html.twig", ["must_login" => true]);
        }
        elseif(isset($_GET["page"]) && $_GET["page"] == "mot-de-passe" && isset($_GET["edit"]) && $_GET["edit"] == Session::get("id", "user")) {
            return $this->edit_password(filter_input(INPUT_POST, 'old_password'), filter_input(INPUT_POST, 'new_password'), filter_input(INPUT_POST, 'conf_new_password'));
        }
        else {
            return $template->render([
                // "msg_password" => $this->edit_password(filter_input(INPUT_POST, 'old_password'), filter_input(INPUT_POST, 'new_password'), filter_input(INPUT_POST, 'conf_new_password')),
            ]);
        }
    }

    public function view_fidelity($template) {
        if(!Session::get("user")) {
            $this->twig->display("front/home.html.twig", ["must_login" => true]);
        }
        else {
            return $template->render([
                // "x" => $x,
            ]);
        }
    }

    public function view_logout() {
        return $this->logout();
    }
}
