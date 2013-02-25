<?php
	/* 
	Récupération dossier musique
	@return	le dossier ou false si erreur
	*/
	function getMusicPath(){
		if(isConnected()){
			$id = getId();

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('SELECT music_folder FROM user WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $id[0],
				'password' => $id[1]
			));

			if($donnees = $query->fetch()){$query->closeCursor();return $donnees['music_folder'];}
			else{$query->closeCursor();return false;}
		}
		else{return false;}
	}

	/* 
	Récupération dossier vidéo
	@return	le dossier ou false si erreur
	*/
	function getVideoPath(){
		if(isConnected()){
			$id = getId();

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('SELECT video_folder FROM user WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $id[0],
				'password' => $id[1]
			));

			if($donnees = $query->fetch()){$query->closeCursor();return $donnees['video_folder'];}
			else{$query->closeCursor();return false;}
		}
		else{return false;}
	}

	/* 
	Récupération des podcast
	@return	la liste des podcasts ou false si erreur
	*/
	function getPodcastUrl(){
		if(isConnected()){
			$id = getId();

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('SELECT podcast FROM user WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $id[0],
				'password' => $id[1]
			));

			if($donnees = $query->fetch()){$query->closeCursor();return $donnees['podcast'];}
			else{$query->closeCursor();return false;}
		}
		else{return false;}
	}

	/* 
	Récupération session lastfm
	@return	la session ou false si erreur
	*/
	function getLastfmSession(){
		if(isConnected()){
			$id = getId();

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('SELECT lastfm_session FROM user WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $id[0],
				'password' => $id[1]
			));

			if($donnees = $query->fetch()){$query->closeCursor();return $donnees['lastfm_session'];}
			else{$query->closeCursor();return false;}
		}
		else{return false;}
	}

	/* 
	Récupération de l'état admin
	@return	true si admin, false sinon
	*/
	function isAdmin(){
		if(isConnected()){
			$id = getId();

			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$bdd = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD, $pdo_options);
			$query=$bdd->prepare('SELECT admin FROM user WHERE name =:name && password =:password');
			$query->execute(array(
				'name' => $id[0],
				'password' => $id[1]
			));

			if($donnees = $query->fetch()){
				$query->closeCursor();
				$admin = ($donnees['admin'] == 1) ? true:false;
				return $admin;
			}
			else{$query->closeCursor();return false;}
		}
		else{return false;}
	}
?>