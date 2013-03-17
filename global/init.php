<?php
	session_start();	
	require_once('config.php');
	
	// Désactivation des guillemets magiques
	ini_set('magic_quotes_runtime', 0);

	if (1 == get_magic_quotes_gpc())
	{
		function remove_magic_quotes_gpc(&$value) {
	
			$value = stripslashes($value);
		}
		array_walk_recursive($_GET, 'remove_magic_quotes_gpc');
		array_walk_recursive($_POST, 'remove_magic_quotes_gpc');
		array_walk_recursive($_COOKIE, 'remove_magic_quotes_gpc');
	}
?>