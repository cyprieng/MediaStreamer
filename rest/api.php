<?php
	header ("Content-Type:text/xml");
	require_once('../global/init.php');
	require_once(ROOT_PATH.MODELE_PATH.'login.php');
	require_once(ROOT_PATH.MODELE_PATH.'settings.php');
	require_once(ROOT_PATH.MODELE_PATH.'home.php');
	require_once(ROOT_PATH.MODELE_PATH.'podcast.php');
	require_once(ROOT_PATH.MODELE_PATH.'scrobbler.php');
	require_once(ROOT_PATH.MODELE_PATH.'getid3/getid3.php');


	//On vérifit si tout les paramètres sont transmis
	$missing_parameter = '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="failed" version="1.1.0"><error code="10" message="Required parameter is missing"/></subsonic-response>';
	if(empty($_GET['u']) || empty($_GET['p']) || empty($_GET['v']) || empty($_GET['c'])){exit($missing_parameter);}

	//Si le pass est en hexa, on le décode
	function hextostr($hex){
		$str='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$str .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $str;
	}
	if(preg_match('#^enc:#', $_GET['p'])){
		$passToStr = hextostr($_GET['p']);

		$pass = '';
		for($i = 0;$i <= strlen($passToStr)-1;$i++){
			if(preg_match('/^[a-z0-9_-]{1}$/', $passToStr[$i])){
				$pass .= $passToStr[$i];
			}
		}
	}
	else{$pass = $_GET['p'];}

	//On test le login
	$login = testLogin($_GET['u'], sha1($_GET['u'].$pass));
	if(!$login){exit('<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="failed" version="1.1.0"><error code="40" message="Wrong username or password"/></subsonic-response>');}

	//On récupère les infos sur l'user
	$user = getUserArray();
	foreach($user as $key => $us){
		if($us['name'] == $_GET['u']){$user = $user[$key];break;}
	}

	$api = $_GET['api'];

	switch($api){ //cf Subsonic API
		case 'ping':
			echo '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="ok" version="1.1.1"></subsonic-response>';
		break;

		case 'getLicense':
			echo '<subsonic-response xmlns="http://subsonic.org/restapi" status="ok" version="1.8.0">
<license valid="true" email="demo@subsonic.org" key="27608bf44d8dceed3a90c1b608dc4bea" date="2009-02-26T12:24:19" />
</subsonic-response>';
		break;

		case 'getMusicFolders':
			echo '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="ok" version="1.1.1"><musicFolders><musicFolder id="1" name="Music"/></musicFolders></subsonic-response>';
		break;

		case 'getIndexes':
			$music_library = getArborescence($user['music_folder'], explode(', ', MUSIC_EXTENSION));
			unset($music_library[0]); //On supprime le nom du dossier racine
			$library_xml = '<artist name="Podcast" id="0" />'; $i=1;

			foreach($music_library as $arbo){ //On ajoute tous les dossiers
				$library_xml .= '<artist name="'.substr(strrchr($arbo[0],'/'),1).'" id="'.$i.'" />';
				$i++;
			}

			echo '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="ok" version="1.1.1"><indexes lastModified="'.time().'">'.$library_xml.'</indexes></subsonic-response>';
		break;

		case 'getMusicDirectory':
			if(empty($_GET['id']) && $_GET['id'] != 0){exit($missing_parameter);}

			$library = getArborescence($user['music_folder'], explode(', ', MUSIC_EXTENSION));

			if(preg_match('#^0#', $_GET['id'])){ //Podcast
				$podcast_library = getPodcast(explode(',', $user['podcast']));
				$library_xml = '';$i=0;

				if($_GET['id'] == '0'){ //Racine podcast
					$name = 'Podcast';
					foreach($podcast_library as $podcast){ //On boucle les podcasts
						$library_xml .= '<child id="'.$_GET['id'].'c'.$i.'" parent="'.$_GET['id'].'" title="'.substr(strrchr($podcast[0],'/'),1).'" isDir="true" />';
						$i++;
					}
				}
				else{
					$id = explode('c', $_GET['id']);
					$id = $id[1];

					$podcast_library = $podcast_library[$id]; //On récupère le podcast voulu
					$name = substr(strrchr($podcast_library[0],'/'),1);
					unset($podcast_library[0]); //On supprime le nom

					foreach($podcast_library as $podcast){ //On parcours les item
						$library_xml .= '<child id="'.$_GET['id'].'c'.$i.'" parent="'.$_GET['id'].'" title="'.preg_replace('#"#', "''", Trim($podcast[1])).'" isDir="false" album="'.$name.'" artist="'.$name.'" track="'.$i.'" type="podcast"/>';
						$i++;
					}
				}
				echo '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="ok" version="1.4.0"><directory id="'.$_GET['id'].'" name="'.$name.'">'.$library_xml.'</directory></subsonic-response>';
				exit('');

			}
			else if(!isset($library[$_GET['id']])){ //Musique sous-dossier
				$id = explode('c', $_GET['id']);
				
				$music_library = $library;
				
				for($i=1;$i<=count($id);$i++){ //On parcour les dossier définit dans $id
					$music_library = $music_library[$id[$i-1]];
				}
			}
			else{ //Musique dans racine
				$music_library = $library[$_GET['id']];
			}

			$artistName = substr(strrchr($music_library[0],'/'),1);
			unset($music_library[0]); //On supprime le nom de l'artiste
			$library_xml = '';$i=1;

			foreach($music_library as $arbo){ //On parcours le dossier
				if(is_array($arbo) && is_dir($arbo[0])){ //Dossier
					$library_xml .= '<child id="'.$_GET['id'].'c'.$i.'" parent="'.$_GET['id'].'" title="'.substr(strrchr($arbo[0],'/'),1).'" artist="'.$artistName.'" isDir="true" />';
				}
				else{ //Fichier
					//On récupère les tag de la musique
					$getID3 = new getID3;
					$tag = $getID3->analyze($arbo);
					$tag['bitrate'] = floor($tag['bitrate'] / 1000);
					$tag['tags_html']['id3v1']['album'][0] = ($tag['tags_html']['id3v1']['album'][0] != '') ? $tag['tags_html']['id3v1']['album'][0] : 'unknow';
					$tag['tags_html']['id3v1']['artist'][0] = ($tag['tags_html']['id3v1']['artist'][0] != '') ? $tag['tags_html']['id3v1']['artist'][0] : 'unknow';
					$tag['tags_html']['id3v1']['year'][0] = ($tag['tags_html']['id3v1']['year'][0] != '') ? $tag['tags_html']['id3v1']['year'][0] : '1970';
					$tag['tags_html']['id3v1']['genre'][0] = ($tag['tags_html']['id3v1']['genre'][0] != '') ? $tag['tags_html']['id3v1']['genre'][0] : 'unknow';

					$library_xml .= '<child id="'.$_GET['id'].'c'.$i.'" parent="'.$_GET['id'].'" title="'.substr(strrchr($arbo,'/'),1).'" isDir="false" album="'.$tag['tags_html']['id3v1']['album'][0].'" artist="'.$tag['tags_html']['id3v1']['artist'][0].'" track="'.$i.'" year="'.$tag['tags_html']['id3v1']['year'][0].'" genre="'.$tag['tags_html']['id3v1']['genre'][0].'" size="'.$tag['filesize'].'" contentType="'.$tag['mime_type'].'" suffix="'.$tag['fileformat'].'" duration="'.floor($tag['playtime_seconds']).'" bitRate="'.$tag['bitrate'].'" path="'.$arbo.'"/>';
				}
				$i++;
			}

			echo preg_replace('#&#', '&amp;', '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="ok" version="1.4.0"><directory id="'.$_GET['id'].'" name="'.$artistName.'">'.$library_xml.'</directory></subsonic-response>');
		break;

		case 'stream':
			if(empty($_GET['id'])){exit($missing_parameter);} //On vérifit les paramètres

			$library = getArborescence($user['music_folder'], explode(', ', MUSIC_EXTENSION));

			if(preg_match('#^0#', $_GET['id'])){ //Podcast
				//On récupère le podcast
				$podcast_library = getPodcast(explode(',', $user['podcast']));
				$id = explode('c', $_GET['id']);
				$music_library = $podcast_library[$id[1]][$id[2]+1][0][0];

			}
			else if(!isset($library[$_GET['id']])){ //Musique dans sous dossier
				$id = explode('c', $_GET['id']);
				
				$music_library = $library;
				
				for($i=1;$i<=count($id);$i++){ //On parcour les dossier définit dans $id
					$music_library = $music_library[$id[$i-1]];
				}
			}
			else{ //Musique dans dossier racine
				$music_library = $library[$_GET['id']];
			}

			//On définit les variables pour media.php
			$_GET['file'] = $music_library;
			$_GET['p'] = sha1($_GET['u'].$pass);

			scrobble($music_library, $user['lastfm_session']); //On scrobble
			require(ROOT_PATH.MODELE_PATH.'media.php');
		break;

		case 'scrobble':
			if(empty($_GET['id'])){exit($missing_parameter);}

			$library = getArborescence($user['music_folder'], explode(', ', MUSIC_EXTENSION));

			if(!isset($library[$_GET['id']])){ //Musique dns sous-dossier
				$id = explode('c', $_GET['id']);
				
				$music_library = $library;
				
				for($i=1;$i<=count($id);$i++){ //On parcour les dossier définit dans $id
					$music_library = $music_library[$id[$i-1]];
				}
			}
			else{ //Musique dans dossier racine
				$music_library = $library[$_GET['id']];
			}

			scrobble($music_library, $user['lastfm_session']); //On scrobble
			echo '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="ok" version="1.8.0"></subsonic-response>';
		break;

		default:
			echo '<?xml version="1.0" encoding="UTF-8"?><subsonic-response xmlns="http://subsonic.org/restapi" status="failed" version="1.1.0"><error code="70" message="The requested data was not found"/></subsonic-response>';
	}
?>