<?php
class controleur {

	public function ajouterRDV() {
		$corpsRequete = file_get_contents('php://input');
		
		if($json = json_decode($corpsRequete, true)) {
			if(isset($json["dateHeureRDV"]) && isset($json["idPatient"]) && isset($json["idMedecin"])) {
				$ajout = (new rdv)->insertRDV();
				
				if($ajout === true) {
					http_response_code(201);
					$json = '{ "code":201, "message": "Rendez-vous pour le patient au matricule '.$json["idPatient"].' ajoutée." }';
					(new vue)->afficherJSON($json);
				}
				else {
					http_response_code(500);
					
					$json = '{ "code":500, "message": "Erreur lors de l\'insertion." }';
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
	
	class controleur {

		public function consulterRDV() {

			if(isset($_SESSION["idPatient"])) {
				$consulter = (new patient)->getRDVPatient($_SESSION["idPatient"]));
				
				if($consulter == true) {
					http_response_code(201);
					//$json = '{ "nom": "'.$consulter["nomPatient"].'", "prenom": "'.$consulter["prenomPatient"].'", "rue": "'.$consulter["ruePatient"].'", "cp": "'.$consulter["cpPatient"].'", "ville": "'.$consulter["villePatient"].'", "tel": "'.$consulter["telPatient"].'" }';

					$json = '{ ';
					foreach ($consulter as $unRDV) {
						$json = $json.'[ "date": '.$consulter["dateHeureRdv"].', "unPatient": '.$consulter["idPatient"].', "unMedecin": '.$consulter["idMedecin"].']';
					}
					$json = $json.' }';

					(new vue)->afficherJSON($json);
				}
				else {
					http_response_code(500);
					
					$json = '{ "code":500, "message": "Erreur lors de l\'insertion." }';
					(new vue)->afficherJSON($json);
				}
			}
			else {
				http_response_code(400);
			
				$json = '{ "code":400, "message": "Données manquantes." }';
				(new vue)->afficherJSON($json);
			}
		}
	}
}