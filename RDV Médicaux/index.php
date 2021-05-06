<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// Test de connexion à la base
$config = parse_ini_file("config.ini");
try {
	$pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e) {
	echo "<h1>Erreur de connexion à la base de données :</h1>";
	echo $e->getMessage();
	exit;
}

setlocale(LC_TIME, "fr_FR.UTF-8");
date_default_timezone_set("Europe/Paris");

// Chargement des fichiers MVC
require("control/controleur.php");
require("view/vue.php");
require("model/rdv.php");
require("model/patient.php");
require("model/authentification.php");

// Routes
if(isset($_GET["action"])) {
	switch($_GET["action"]) {
		case "rdv":
			switch($_SERVER["REQUEST_METHOD"]) {
				case "GET":
					(new controleur)->consulterRDV();
					break;
				case "POST":
					(new controleur)->ajouterRDV();
					break;
				case "PUT":
					(new controleur)->modifierRDV();
					break;
				case "DELETE":
					(new controleur)->annulerRDV();
					break;
			}
			break;

		case "connexion":
			(new controleur)->connexion();
			break;

		case "inscription":
			(new controleur)->inscription();
			break;
			
		// Route par défaut : erreur 404
		default:
			(new controleur)->erreur404();
			break;
	}
}
else {
	// Pas d'action précisée = afficher l'accueil
	$json = '{ "code":200, "message": "Bienvenue dans l\'API sale bg !" }';
	(new vue)->afficherJSON($json);
}