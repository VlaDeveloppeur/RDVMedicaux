<?php

class authentification {
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
	public function getAll() {
		$sql = "SELECT * FROM authentification";
		
		$req = $this->pdo->prepare($sql);
		$req->execute();
		
		return $req->fetchAll();
	}

	public function insertConnexion($token, $idPatient){
		$sql ="SELECT * FROM authentification WHERE token = :token AND ipAppareil = :ip";
		$req = $this->pdo->prepare($sql);
		$req->bindParam(":token", $token, PDO::PARAM_STR);
		$ip = $this->getIp();
		$req->bindParam(":ip", $ip, PDO::PARAM_STR);

		$ip = $this->getIp();
		
		if (!$req->execute()) {
			$this->insertion($token, $idPatient, $ip);
		}else{
			$sql = "DELETE FROM authentification WHERE idPatient = :id";
			$req = $this->pdo->prepare($sql);
			$req->bindParam(":id", $idPatient["idPatient"], PDO::PARAM_STR);
			$req->execute();

			$this->insertion($token, $idPatient, $ip);
		}
	}

	public function insertion($token, $idPatient, $ip){
		$sql = "INSERT INTO authentification(token, idPatient, ipAppareil) VALUES (:token, :idPatient, :ipAppareil);";

		$req = $this->pdo->prepare($sql);
		$req->bindParam(":token", $token, PDO::PARAM_STR);
		$req->bindParam(":idPatient", $idPatient["idPatient"], PDO::PARAM_STR);
		$req->bindParam(":ipAppareil", $ip, PDO::PARAM_STR);
		$req->execute();
	}

	/*public function getPatientConnexion(){
		$sql = "SELECT * FROM authentification WHERE idPatient = :id";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(":id", $id, PDO::PARAM_STR);
		$req->execute();
		
		if ($req->fetch()) {
			return true;
		}else{
			return false;
		}
	}*/

	public function getIdPatient($token){
		$sql = "SELECT idPatient FROM authentification WHERE token = :token";
		$req = $this->pdo->prepare($sql);
		$req->bindParam(":token", $token, PDO::PARAM_STR);
		$req->execute();
		return $req->fetch();
	}

	public function connVerif($token, $ip){
		$sql = "SELECT * FROM authentification WHERE token = :token AND ipAppareil = :ip";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(":token", $token, PDO::PARAM_STR);
		$req->bindParam(":ip", $ip, PDO::PARAM_STR);
		$req->execute();
		
		if ($req->fetch()) {
			return true;
		}else{
			return false;
		}
	}

	public function getIp(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
          $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
          $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
	
}