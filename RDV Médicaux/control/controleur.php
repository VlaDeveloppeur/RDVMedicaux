<?php
class controleur {
	
	public function erreur404() {
		(new vue)->erreur404();
	}

	public function connexion() {
		$corpsRequete = file_get_contents('php://input');

		if($json = json_decode($corpsRequete, true)) {
			$ajout = (new patient)->connexion($json["login"],$json["motdepasse"]);

			if($ajout === true) {
				$token = bin2hex(random_bytes(30));
				$idPatient = (new patient)->getIdPatient($json["login"],$json["motdepasse"]);

				//(new authentification)->updateConnexion($token, $idPatient);
				(new authentification)->insertConnexion($token, $idPatient);

				http_response_code(201);
				$json = '{ "code":201, "message": "Connexion confirmée", "token" : "'.$token.'" }';
				(new vue)->afficherJSON($json);
			}
			else {
				http_response_code(500);
				
				$json = '{ "code":500, "message": "Erreur connexion." }';
				(new vue)->afficherJSON($json);
			}
		}
		else {
			http_response_code(400);
			
			$json = '{ "code":400, "message": "Données incorrectes." }';
			(new vue)->afficherJSON($json);
		}
		
	}

	public function inscription() {
		$corpsRequete = file_get_contents('php://input');

		if($json = json_decode($corpsRequete, true)) {
			//(new patient)->inscriptionClient($_POST['nom'], $_POST['prenom'], $_POST['email'], password_hash($_POST['motdepasse'], PASSWORD_BCRYPT), $_POST['adresse'], $_POST['cp'], $_POST['ville'], $_POST['tel']);
			$ajoutpatient = (new patient)->inscription($json["nom"], $json["prenom"], $json["login"], password_hash($json["motdepasse"], PASSWORD_BCRYPT), $json["adresse"], $json["cp"], $json["ville"], $json["tel"]);

			if($ajoutpatient === true) {
				http_response_code(201);
				$json = '{ "code":201, "message": "Inscription confirmé de Nom: '.$json["nom"].' Prenom : '.$json["prenom"].'" }';
				(new vue)->afficherJSON($json);
			}
			else {
				http_response_code(500);
				
				$json = '{ "code":500, "message": "Erreur inscription." }';
				(new vue)->afficherJSON($json);
			}
		}
		else {
			http_response_code(400);
			
			$json = '{ "code":400, "message": "Le corps de la requête est invalide." }';
			(new vue)->afficherJSON($json);
		}	
	}

	public function ajouterRDV() {
		$corpsRequete = file_get_contents('php://input');
		
		if($json = json_decode($corpsRequete, true)) {
			if(isset($json["token"])) {
				if ((new authentification)->connVerif($json["token"], (new authentification)->getIp())){
					$idPatient = (new authentification)->getIdPatient($json["token"]);
					if(isset($json["dateHeureRDV"]) && isset($json["idMedecin"])) {
						$ajout = (new rdv)->insertRDV($json["dateHeureRDV"], $idPatient[0], $json["idMedecin"]);
						if($ajout === true) {
							http_response_code(201);
							$json = '{ "code":201, "message": "Rendez-vous pour le patient au matricule '.$idPatient[0].' ajoutée." }';
							(new vue)->afficherJSON($json);
						}
						else {
							http_response_code(500);
							
							$json = '{ "code":500, "message": "'.$ajout.'" }';
							(new vue)->afficherJSON($json);
						}
					}
					else {
						http_response_code(400);
					
						$json = '{ "code":400, "message": "Données manquantes." }';
						(new vue)->afficherJSON($json);
					}
				}else{
					http_response_code(403);
			
					$json = '{ "code":403, "message": "Accès non autorisé." }';
					(new vue)->afficherJSON($json);
				}
			}
			else {
				http_response_code(400);
				
				$json = '{ "code":400, "message": "Données manquantes." }';
				(new vue)->afficherJSON($json);
			}
		}
		else {
			http_response_code(400);
			
			$json = '{ "code":400, "message": "Le corps de la requête est invalide." }';
			(new vue)->afficherJSON($json);
		}
	}

	public function consulterRDV() {
		//$corpsRequete = file_get_contents('php://input');
		//if($json = json_decode($corpsRequete, true)) {
			if (isset($_GET["token"])){
				if ((new authentification)->connVerif($_GET["token"], (new authentification)->getIp())){
					$consulter = (new patient)->getRDVPatient((new authentification)->getIdPatient($_GET["token"]));
					
					//$consulter .= (new patient)->getPatient((new authentification)->getIdPatient($_GET["token"]));

					if($consulter == true) {
						http_response_code(201);
						//$json = '{ "nom": "'.$consulter["nomPatient"].'", "prenom": "'.$consulter["prenomPatient"].'", "rue": "'.$consulter["ruePatient"].'", "cp": "'.$consulter["cpPatient"].'", "ville": "'.$consulter["villePatient"].'", "tel": "'.$consulter["telPatient"].'" }';
						
						$json = json_encode($consulter);
						(new vue)->afficherJSON($json);
					}
					else {
						http_response_code(500);
						
						$json = '{ "code":500, "message": "Erreur lors de l\'insertion." }';
						(new vue)->afficherJSON($json);
					}
				}else{
					http_response_code(403);
			
					$json = '{ "code":403, "message": "Accès non autorisé." }';
					(new vue)->afficherJSON($json);
				}
			}else{
				http_response_code(403);
			
				$json = '{ "code":401, "message": "Vous n\'étes pas connecter." }';
				(new vue)->afficherJSON($json);
			}
		/*}
		else {
			http_response_code(400);
			
			$json = '{ "code":400, "message": "Le corps de la requête est invalide." }';
			(new vue)->afficherJSON($json);
		}*/
	}

	public function annulerRDV() {
		$corpsRequete = file_get_contents('php://input');

		if($json = json_decode($corpsRequete, true)) {
			if(isset($json["token"])) {
				if ((new authentification)->connVerif($json["token"], (new authentification)->getIp())){
					if(isset($json["id"])) {
						$supprimer = (new rdv)->annulerRDV($json["id"]);
						if($supprimer == true) {
							http_response_code(200);
					
							$json = '{ "code":200, "message": "La personne a été suprimée." }';
							(new vue)->afficherJSON($json);
						}
						else 
						{
							http_response_code(400);
					
							$json = '{ "code":400, "message": "Impossible de supprimer cette personne." }';
							(new vue)->afficherJSON($json);
						}
					}
					else 
					{
						(new vue)->erreur404();
					}
				}else{
					http_response_code(403);
			
					$json = '{ "code":403, "message": "Accès non autorisé." }';
					(new vue)->afficherJSON($json);
				}
			}
			else 
			{
				http_response_code(400);
				
				$json = '{ "code":400, "message": "Données manquantes." }';
				(new vue)->afficherJSON($json);
			}
		}
		else {
			
			http_response_code(400);
		
			$json = '{ "code":400, "message": "Le corps de la requête est invalide." }';
			(new vue)->afficherJSON($json);
		}
	}

	public function modifierRDV() {
		$corpsRequete = file_get_contents('php://input');

		if($json = json_decode($corpsRequete, true)) {
			if (isset($json["token"])) {
				if ((new authentification)->connVerif($json["token"], (new authentification)->getIp())){
					if(isset($json["idRdv"]) && count($json) >= 2) {
						$dateHeureRDV = null;
						//$idPatient = null;
						//$idMedecin = null;
						
						if(isset($json["dateHeureRDV"])) {
							$dateHeureRDV = $json["dateHeureRDV"];
						}
						/*if(isset($json["idPatient"])) {
							$idPatient = $json["idPatient"];
						}*/
						/*if(isset($json["idMedecin"])) {
							$idMedecin = $json["idMedecin"];
						}*/
						
						$modif = (new rdv)->modifierRDV($json["idRdv"], $dateHeureRDV);
						
						if($modif == true) {
							http_response_code(200);
							if (isset($dateHeureRDV)) {
								//setlocale(LC_TIME, "fr_FR");
								
								$date = new DateTime($dateHeureRDV);
								$heure = date_format($date, "H")."h".date_format($date, "i");
								//setlocale(LC_TIME, "fr_FR");
								//setlocale(LC_TIME, "fr_FR", "French");

								
								$json = '{ "code":200, "message": "Votre rendez-vous a était reporter le '.strftime("%A %d %B %G", strtotime($dateHeureRDV)).' à '.$heure.'." }';
							}else{
								$json = '{ "code":200, "message": "Les modifications liées a ce rendez-vous ont était mise à jour." }';
							}
							(new vue)->afficherJSON($json);
						}
						else {
							http_response_code(500);
							
							$json = '{ "code":500, "message": "Erreur lors de la modification." }';
							(new vue)->afficherJSON($json);
						}
					}
					else {
						http_response_code(400);
					
						$json = '{ "code":400, "message": "Données manquantes." }';
						(new vue)->afficherJSON($json);
					}
				}else{
					http_response_code(403);
		
					$json = '{ "code":403, "message": "Accès non autorisé." }';
					(new vue)->afficherJSON($json);
				}
			}else{
				http_response_code(400);
				
				$json = '{ "code":400, "message": "Données manquantes." }';
				(new vue)->afficherJSON($json);
			}
		}
		else {
			
			http_response_code(400);
		
			$json = '{ "code":400, "message": "Le corps de la requête est invalide." }';
			(new vue)->afficherJSON($json);
		}
	}
	
}