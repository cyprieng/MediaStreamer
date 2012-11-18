<?php
	/* 
	Récupération d'arborescence
	@params	folder		Dossier à explorer
	@params	extension	Extension des fichiers à afficher
	@return	tableau du type array[0] = nom dossier; array[1...2...] = nom fichier; Si sous-dossier array[?][] = array sous-dossier
	*/
	function getArborescence($folder, $extension){
		$pointeur = opendir($folder); //on ouvre un pointeur sur le repertoire
		$arborescence[] = $folder; //Première case => nom dossier

		while($file = readdir($pointeur)){ //pour chaque fichier et dossier
			if(($file != '.') && ($file != '..')){ //on ne traite pas les . et ..
				if (is_dir($folder.'/'.$file)){ //si c'est un dossier, on le lit   
					$arborescence[] = getArborescence($folder.'/'.$file, $extension);
				}
				else if(in_array(substr(strrchr($file,'.'),1), $extension)){ //Si fichier on vérifit l'extension
					$arborescence[] = $folder.'/'.$file;
				}
			}
		}

		closedir($pointeur); //fermeture du pointeur
		return $arborescence;
	}
?>