<?php
// Timezone
date_default_timezone_set("Europe/Paris");


// Includes
require_once("controller/BackController.class.php");
require_once("controller/FrontController.class.php");
require_once("controller/SecurityController.class.php");


// Session
Session::start();
Session::generate_token();


// Instantiations
$backController = new BackController;
$frontController = new FrontController;
$securityController = new SecurityController;


// Routes
if(isset($_GET["page"])) $page = $_GET["page"];
else $page = "";

switch ($page) {
	// FrontController
	case "":
		$template = $frontController->twig->load("front/home.html.twig");
		echo $frontController->view_home($template);
		break;

	case "carte":
		$template = $frontController->twig->load("front/menu_carte.html.twig");
		echo $frontController->view_menu_carte($template);	
		break;

	case "commande":
		$template = $frontController->twig->load("front/order.html.twig");
		echo $frontController->view_order($template);	
		break;

	case "reservation":
		$template = $frontController->twig->load("front/booking.html.twig");
		echo $frontController->view_booking($template);	
		break;

	case "infos":
		$template = $frontController->twig->load("front/infos.html.twig");
		echo $frontController->view_infos($template);	
		break;

	case "avis-clients":
		$template = $frontController->twig->load("front/reviews.html.twig");
		echo $frontController->view_reviews($template);	
		break;

	case "contact":
		$template = $frontController->twig->load("front/contact.html.twig");
		echo $frontController->view_contact($template);	
		break;


	// SecurityController
	case "inscription":
		$template = $securityController->twig->load("security/registration.html.twig");
		echo $securityController->view_registration($template);
		break;

	case "connexion":
		// no template : json AJAX
		echo $securityController->view_login();
		break;

	case "mon-compte":
		$template = $securityController->twig->load("security/account.html.twig");
		echo $securityController->view_account($template);
		break;

	case "mot-de-passe":
		$template = $securityController->twig->load("security/account.html.twig");
		echo $securityController->view_password($template);
		break;

	case "fidelite":
		$template = $securityController->twig->load("security/fidelity.html.twig");
		echo $securityController->view_fidelity($template);
		break;

	case "deconnexion":
		// no template : redirect to home
		echo $securityController->view_logout();
		break;


	// AdminController
	case "admin":
		$template = $backController->twig->load("back/admin_backoffice.html.twig");
		echo $backController->view_admin_backoffice($template);
		break;

	case "admin-commandes":
		$template = $backController->twig->load("back/admin_orders.html.twig");
		echo $backController->view_admin_orders($template);
		break;

	case "admin-reservations":
		$template = $backController->twig->load("back/admin_bookings.html.twig");
		echo $backController->view_admin_bookings($template);
		break;

	case "admin-carte":
		$template = $backController->twig->load("back/admin_products.html.twig");
		echo $backController->view_admin_products($template);
		break;

	case "admin-utilisateurs":
		$template = $backController->twig->load("back/admin_users.html.twig");
		echo $backController->view_admin_users($template);
		break;

	case "admin-avis":
		$template = $backController->twig->load("back/admin_reviews.html.twig");
		echo $backController->view_admin_reviews($template);
		break;

	
	// Default (error 404)
	default:
		header("HTTP/1.0 404 Not Found");
		echo $frontController->twig->render("404.html.twig", [
			// "x" => $x,
		]);
		break;
}
