<?php

class patient {
	
	// Objet PDO servant à la connexion à la base
	private $pdo;

	// Connexion à la base de données
	public function __construct() {
		$config = parse_ini_file("config.ini");
		
		try {
			$this->pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	// Connexion d'un Patient (vérification login + mot de passe)
	public function connexion($login, $mdp) {
		$sql = "SELECT idPatient, motDePassePatient FROM patient WHERE loginPatient = :login";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':login', $login, PDO::PARAM_STR);
		//$req->bindParam(':mdp', $mdp, PDO::PARAM_STR);
		$req->execute();
		
		$ligne = $req->fetch();

		if($ligne != false) {
			// Patient existant

			// On vérifie si le hash du mot de passe stocké dans la base correspond au mot de passe saisi dans le formulaire
			if(password_verify($mdp, $ligne["motDePassePatient"])) {
				// Connexion vérifiée
				return true;
			}
			else {
				// Mot de passe incorrect
				return false;
			}
		}
		else {
			// Patient inconnu
			return false;
		}
	}

	// Inscrire un Patient
	public function inscription($nom, $prenom, $login, $motDePasse, $adresse, $cp, $ville, $tel) {
		$sql = "INSERT INTO patient (`nomPatient`, `prenomPatient`, `loginPatient`, `motDePassePatient`, `ruePatient`, `cpPatient`, `villePatient`, `telPatient`) VALUES (:nom, :prenom, :login, :motDePasse, :adresse, :cp, :ville, :tel)";

		$req = $this->pdo->prepare($sql);
		$req->bindParam(':nom', $nom, PDO::PARAM_STR);
		$req->bindParam(':prenom', $prenom, PDO::PARAM_STR);
		$req->bindParam(':login', $login, PDO::PARAM_STR);
		$req->bindParam(':motDePasse', $motDePasse, PDO::PARAM_STR);
		$req->bindParam(':adresse', $adresse, PDO::PARAM_STR);
		$req->bindParam(':cp', $cp, PDO::PARAM_STR);
		$req->bindParam(':ville', $ville, PDO::PARAM_STR);
		$req->bindParam(':tel', $tel, PDO::PARAM_STR);
		
		if ($req->execute()) {
			return true;
		}else{
			return false;
		}
	}

	public function getPatient($id) {
		$sql = "SELECT * FROM patient WHERE idPatient = :id";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(":id", $id, PDO::PARAM_STR);
		$req->execute();
		
		return $req->fetch();
	}

	public function getRDVPatient($id) {
		$today = date("Y-m-d H:i");
		$sql = "SELECT idRdv, dateHeureRdv, idMedecin
		FROM rdv
		WHERE idPatient = :id  AND `dateHeureRdv` > :dateHeure 
		ORDER BY dateHeureRdv ASC";

		$req = $this->pdo->prepare($sql);
		$req->bindParam(":id", $id["idPatient"], PDO::PARAM_INT);
		$req->bindParam(":dateHeure", $today, PDO::PARAM_STR);
		$req->execute();
		
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getIdPatient($login, $mdp){
		$sql = "SELECT idPatient FROM patient WHERE loginPatient = :login";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(":login", $login, PDO::PARAM_STR);
		$req->execute();
		
		return $req->fetch();
	}
}