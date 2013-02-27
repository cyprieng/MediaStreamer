<?php
	//On inclut le modèle
	require_once(MODELE_PATH.'lyrics.php');

	mustConnected(); //On vérifit que l'utilisateur est connecté

	//On affiche les paroles
	echo '<lyrics>'.lyrics(urldecode($_GET['file'])).'</lyrics>';
?>