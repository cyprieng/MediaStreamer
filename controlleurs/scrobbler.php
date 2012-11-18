<?php
	//On inclut le modèle
	require_once(MODELE_PATH.'scrobbler.php');

	mustConnected(); //On vérifit que l'utilisateur est connecté
	 
	//On scrobble le fichier
	scrobble($_GET['file']);
?>