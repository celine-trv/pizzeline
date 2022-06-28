<?php
require_once("utility/Session.class.php");
require_once("utility/PHPMailer/Mailer.class.php");
require_once("model/Booking.class.php");
require_once("model/BookingManager.class.php");
require_once("model/Orders.class.php");
require_once("model/OrdersManager.class.php");
require_once("model/Product.class.php");
require_once("model/ProductManager.class.php");
require_once("model/Reviews.class.php");
require_once("model/ReviewsManager.class.php");
require_once("model/User.class.php");
require_once("model/UserManager.class.php");
require_once("vendor/autoload.php");        // Twig


class CommonController
{
    public $twig;
    
    // all methods here will be called only by other controllers which are extends by this, so they are protected
    protected function __construct() {
        $this->twig();
        $this->xss();
        $this->csrf();
    }

    
    // Twig
    protected function twig() {
        $loader = new \Twig\Loader\FilesystemLoader("template");
        $this->twig = new \Twig\Environment($loader, [
            // 'cache' => __DIR__ . 'tmp',
            'cache' => false,
            'debug' => true
        ]);
        
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->twig->addGlobal('home', dirname($_SERVER["SCRIPT_NAME"]));
        // $this->twig->addGlobal('actual_url', $_SERVER["REQUEST_URI"]);
        $this->twig->addGlobal('session', isset($_SESSION) ? $_SESSION : null);
        $this->twig->addGlobal('post', isset($_POST) ? $_POST : null);
        $this->twig->addGlobal('get', isset($_GET) ? $_GET : null);
        $this->twig->addGlobal('page', filter_input(INPUT_GET, 'page'));
    }


    // Protection from XSS attack (in addition of twig protection, for exemple when have to use |raw filter)
    protected function xss() {
        foreach($_POST as $key => $value) {
            $_POST[$key] = htmlspecialchars(trim($value));
        }
        foreach($_GET as $key => $value) {
            $_GET[$key] = htmlentities(trim($value));
        }
    }


    // Protection from CSRF attack
    protected function csrf() {
        $post_token = filter_input(INPUT_POST, "token");
        $get_token = filter_input(INPUT_GET, "token");

        if($_POST && !isset($post_token) || isset($post_token) && (!Session::check_token_valid($post_token) || $post_token != Session::get("token"))) {
            $this->twig->display("front/home.html.twig", ["error_csrf" => true]);
            die();
        }           // only delete will be with GET method : edit is with form so POST
        elseif(isset($_GET["delete"]) && (!isset($get_token) && !isset($post_token)) || isset($get_token) && (!Session::check_token_valid($get_token) || $get_token != Session::get("token"))) {
            $this->twig->display("front/home.html.twig", ["error_csrf" => true]);
            die();
        }
    }
}