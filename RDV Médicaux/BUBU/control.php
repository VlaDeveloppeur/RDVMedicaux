<?php

class controleur {
	public function annulerRDV() {
		if(isset($_GET["id"])) {
			$supprimer = (new rdv)->annulerRDV($_GET["id"]);
				if($supprimer == true) {
					http_response_code(200);
			
					$json = '{ "code":200, "message": "La personne a été suprimée." }';
					(new vue)->afficherJSON($json);
				}
				else {
					http_response_code(400);
			
					$json = '{ "code":400, "message": "Impossible de supprimer cette personne." }';
					(new vue)->afficherJSON($json);
				}
			}
			else {
				(new vue)->erreur404();
			}
		}
		else {
			http_response_code(400);
			
			$json = '{ "code":400, "message": "Données manquantes." }';
			(new vue)->afficherJSON($json);
		}
	}

	public function modifierRDV() {
		$corpsRequete = file_get_contents('php://input');
		
		if($json = json_decode($corpsRequete, true)) {
			if(isset($json["idRdv"]) && count($json) >= 2) {
				$dateHeureRDV = null;
				$idPatient = null;
				$idMedecin = null;
				
				if(isset($json["dateHeureRDV"])) {
					$dateHeureRDV = $json["dateHeureRDV"];
				}
				if(isset($json["idPatient"])) {
					$idPatient = $json["idPatient"];
				}
				if(isset($json["idMedecin"])) {
					$idMedecin = $json["idMedecin"];
				}
				
				$modif = (new rdv)->modifierRDV($json["idRdv"], $dateHeureRDV, $idPatient, $idMedecin);
				
				if($modif == true) {
					http_response_code(200);
					$json = '{ "code":200, "message": "Le Rensez-vous au matricule '.$json["idRdv"].' modifiée." }';
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
		}
		else {
			http_response_code(400);
			
			$json = '{ "code":400, "message": "Le corps de la requête est invalide." }';
			(new vue)->afficherJSON($json);
		}
	}
}

?>