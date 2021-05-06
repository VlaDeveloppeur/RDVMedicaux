<?php

class rdv {
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
	
	// Récupérer toutes les catégories
	public function getRDV() {
		$sql = "SELECT * FROM rdv";
		
		$req = $this->pdo->prepare($sql);
		$req->execute();
		
		return $req->fetchAll();
	}

	public function insertRDV($dateHeureRdv, $idPatient, $idMedecin){
		/*if($req->execute()){
			return true;
		} else {
			return false;
		}*/
		try {
			$message = true;
			$sql = "INSERT INTO rdv(dateHeureRdv, idPatient, idMedecin) VALUES (:dateHeureRdv, :idPatient, :idMedecin)";

			$req = $this->pdo->prepare($sql);
			$req->bindParam(":dateHeureRdv", $dateHeureRdv, PDO::PARAM_STR);
			$req->bindParam(":idPatient", $idPatient, PDO::PARAM_STR);
			$req->bindParam(":idMedecin",  $idMedecin, PDO::PARAM_STR);

			$res = $req->execute();
			if ($res === false) {
				$error = $req->errorInfo();
				$message = $error[2];
			}
		} catch (PDOException $e) {
		    $message = $e->getMessage();
		}
		return $message;
	}

	public function annulerRDV($idRdv){
		$sql = "DELETE FROM rdv WHERE idRdv = :idRdv;";

		$req = $this->pdo->prepare($sql);
		$req->bindParam(":idRdv", $idRdv, PDO::PARAM_STR);
		if ($req->execute()) {
			return true;
		}else{
			return false;
		}
	}

	public function modifierRDV($idRdv, $newDate){
		$sql = "UPDATE rdv SET dateHeureRdv = :newDate WHERE idRdv = :idRdv;";

		$req = $this->pdo->prepare($sql);
		$req->bindParam(":idRdv", $idRdv, PDO::PARAM_STR);
		$req->bindParam(":newDate", $newDate, PDO::PARAM_STR);
		//$req->bindParam(":newIdPatient", $newIdPatient, PDO::PARAM_STR);
		//$req->bindParam(":newIdMedecin", $newIdMedecin, PDO::PARAM_STR);
		
		if($req->execute()){
			return true;
		} else {
			return false;
		}
	}
}