# Bienvenue chez Pizzeline 🍕
![w3c-badge](https://img.shields.io/badge/W3C-validation-green?style=for-the-badge)

Pizzeline est un projet full-stack développé en totale autonomie, des maquettes de conception (logo, design, charte graphique) à la mise en production.
(projet en "stand-by" : certaines des fonctionnalités sont encore à venir)


## Technos
### Front-end&emsp; ![twig-badge](https://img.shields.io/badge/Twig-green?style=for-the-badge&color=bacf29) ![html5-badge](https://img.shields.io/badge/HTML5-orange?style=for-the-badge&color=f0632a) ![css3-badge](https://img.shields.io/badge/CSS3-blue?style=for-the-badge&color=3b9ad8) ![javascript-badge](https://img.shields.io/badge/JavaScript-yellow?style=for-the-badge&color=eed94d) ![jquery-badge](https://img.shields.io/badge/jQuery-blue?style=for-the-badge&color=0867af)
### Back-end &emsp; ![poo-badge](https://img.shields.io/badge/POO-lightgrey?style=for-the-badge) ![php-badge](https://img.shields.io/badge/PHP-9cf?style=for-the-badge&color=8a9bd4) ![sql-badge](https://img.shields.io/badge/SQL-orange?style=for-the-badge&color=e68d02) ![ajax-badge](https://img.shields.io/badge/Ajax-blue?style=for-the-badge&color=3f92cb)
### Framework&nbsp; ![bootstrap-badge](https://img.shields.io/badge/Bootstrap-blueviolet?style=for-the-badge&color=8c57d9)

Cette application web est développée en PHP programmation orienté objet, selon une architecture MVC + routeur, avec une base de données relationnelle SQL de 6 tables pour la partie back-end, puis HTML5 / CSS3 (Twig), JavaScript / jQuery et Bootstrap pour la partie front-end. La plupart des formulaires sont traités en Ajax.


## Description
Site d'un restaurant italien conçu sur l'idée que celui du coin de ma rue ne disposait, à ma grande surprise, d'aucune présence internet et cela malgré sa notoriété dans le secteur.

Les utilisateurs peuvent y consulter la carte (menu téléchargeable en pdf), puis commander en ligne avec soit une livraison à domicile dans un périmètre défini, soit en click-and-collect en récupérant les plats sur place. Les internautes auront également la possibilité de réserver une table à l'avance depuis chez eux à n'importe quel moment. Le tout avec une limite calendaire pour les 30 jours à venir.

Une page renseigne les informations sur l'établissement (coordonnées, carte d'accès Google Maps).

Une page de contact présente un formulaire dont les informations sont transmises à l'établissement par email.

Un modal reprend les mentions légales.

Une page personnalisée à l'image du site indique une éventuelle erreur 404.


## Fonctionnalités
- Compte utilisateur sécurisé avec envoi d'un email de confirmation à l'inscription (données du compte modifiables par l'utilisateur, historique de ses commandes et réservations)

- Gestion de Panier intéractif : possibilité de le retrouver au retour sur le site, supprimer ou modifier chaque produit et leur quantité ou le vider entièrement

- Afin d'assurer le service et éviter les abus, les commandes ont un nombre max limité de produits, idem pour la quantité d'un même produit ajouté au panier. Cette valeur peut facilement être modifiée afin de correspondre au choix du restaurateur

- Les formulaires sont créés à partir d'un modèle de macro Twig, conçu pour être ensuite importé dans les différents templates

- La plupart de ces formulaires sont traités en Ajax

- Fidélité : les clients reçoivent des points à chaque commande validée. Points qu'ils pourront ensuite convertir en bon d'achat

- Réservation de table en ligne (à venir)

- Avis clients (à venir)

- Back-Office pour les utilisateurs admin avec fonctionnalités type CRUD :
	- gestion des utilisateurs
	- gestion des commandes
	- gestion des réservations
	- gestion de carte / menu
	- gestion des avis clients

- Popup d'information sur les cookies

- Sécurité : protection contre les failles XSS et CSRF (token), en plus des mesures apportées par Twig, et contrôle strict des données entrantes (style défensif)

- Réécriture d'URL via un fichier .htaccess


## Améliorations
- Lien de vérification dans l'email d'inscription afin de confirmer celle-ci et l'adresse mail de l'utilisateur
- Possibilité de réinitialiser le mot de passe en en créant un nouveau
- Menu téléchargeable via un QR Code en plus du lien


## Configuration
Afin d'exploiter toutes les fonctionnalités de l'application, il est nécessaire de personnaliser certaines informations :
- utility/PHPMailer/Mailer.class.php : modifier les valeurs des propriétés constantes _HOST, _USERNAME et _PASSWORD par celles correspondant aux paramètres de configuration de votre serveur email (ajuster éventuellement les propriétés $mailer selon le besoin)

- model/Manager.trait.php : modifier les valeurs de SGBD, HOST, DB_NAME, USERNAME et PASSWORD par celles correspondant aux paramètres de configuration de votre base de données


## Aperçu
![screenshot](https://github.com/celine-trv/pizzeline/blob/master/screenshot.png)
