<?php
	/* 
	Test le login
	@params	login	Nom de l'utilisateur
	@params	pass	Mot de passe de l'utilisateur hashé
	@return	true si couple ok, false sinon
	*/
	function testLogin($login, $pass){
		if(isset($login) && isset($pass)){
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('SELECT COUNT(*) AS nbr FROM user WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $login,
				'password' => $pass
			));
			$connect=($query->fetchColumn()==0)?0:1;
			$query->CloseCursor();	

			if($connect){return true;}
			else{return false;}
		}
		else{return false;}
	}

	/* 
	Connecte l'utilisateur en créant les cookies et variables de session
	@params	login	Nom de l'utilisateur
	@params	pass	Mot de passe de l'utilisateur non hashé
	*/
	function connect($login, $pass){
		$_SESSION['name'] = $login;
		$_SESSION['pass'] = sha1($login.$pass);

		setcookie('name', $login, time() + 365*24*3600, null, null, false, true);
		setcookie('pass', sha1($login.$pass), time() + 365*24*3600, null, null, false, true);
	}

	/* 
	Déconnecte l'utilisateur en supprimant les cookies et variables de session
	*/
	function deconnect(){
		session_destroy();

		setCookie('name', '', (time() - 3600));
		setCookie('pass', '', (time() - 3600));
	}

	/* 
	Test si l'utilisateur est connecté
	@return	true si connecté, false sinon
	*/
	function isConnected(){
		static $connected = null; //Variable static pour éviter de lancer plusieurs fois des requetes SQL
		if($connected == null){
			$id = getId();
			if(is_array($id)){ //On vérifit les identifiants
				$connected = testLogin($id[0], $id[1]); //On test les identifiants
				return $connected;
			}
			else{
				$connected = false;
				return false;
			}
		}
		else{
			return $connected;
		}
	}

	/* 
	Renvoi l'utilisateur à la page de connexion si il ne l'est pas
	*/
	function mustConnected(){
		if(!isConnected()){
			header('Location: index.php?p=login');
		}
	}

	/* 
	Récupère les identifiants de l'utilisateur actuel en fonction des cookies et session
	@return	array(login, pass) pass étant hashé. Ou false si erreur
	*/
	function getId(){
		if(isset($_SESSION['name']) && isset($_SESSION['pass'])){
			return array($_SESSION['name'], $_SESSION['pass']);
		}
		else if(isset($_COOKIE['name']) && isset($_COOKIE['pass'])){
			$_SESSION['name'] = $_COOKIE['name'];
			$_SESSION['pass'] = $_COOKIE['pass'];
			return array($_COOKIE['name'], $_COOKIE['pass']);
		}
		else{
			return false;
		}
	}
?>