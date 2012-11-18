<?php
	require_once(ROOT_PATH.MODELE_PATH.'getid3/getid3.php'); //Bibliothèque tag audio
	require_once(ROOT_PATH.MODELE_PATH.'user.data.php');

	/* 
	Connection à Lastfm
	@return	session de conection à lastfm
	@see	Lastfm API
	*/
	function connectLastFM(){
		if(!isset($_GET['token'])){ //Si le token n'est pas défini, on redirige l'user sur lastfm
			header("Location: http://www.last.fm/api/auth/?api_key=b00af6e57f866d6ed898336da1b9f836&cb=".urlencode("http://".$_SERVER['SERVER_NAME'].ROOT_PATH_HTML."index.php?p=settings&lastfm=1"));
		}
		else{ //Sinon on récupère les donées de connection
			$authXML = new DOMDocument();

			//On envoit la requête
			$api_sig = md5("api_keyb00af6e57f866d6ed898336da1b9f836methodauth.getSessiontoken".$_GET['token']."bea5b2dcd02de18c1b77d48fba9e206c");
			$authXML->load("http://ws.audioscrobbler.com/2.0/?method=auth.getSession&api_key=b00af6e57f866d6ed898336da1b9f836&token=".$_GET['token']."&api_sig=".$api_sig);

			//On récupère la session
			$session = $authXML->getElementsByTagName("session")->item(0);
			$session = $session->getElementsByTagName("key")->item(0)->nodeValue;
			
			return $session;
		}
		
	}

	/* 
	Scrobble un morceau sur lastfm
	@params	file	Fichier à scrobbler
	@return	Réponse de lastfm
	@see	Lastfm API
	*/
	function scrobble($file, $lastfm_session = null){
		//Si la session lastfm n'est pas transimises, on la récupère
		if($lastfm_session == null){$lastfm_session = getLastfmSession();}

		if(!empty($lastfm_session)){
			$file = urldecode($file);

			//On récupère les tag de la musique
			$getID3 = new getID3;
			$tag = $getID3->analyze($file);

			$timestamp = time();
			$api_sig = md5("api_keyb00af6e57f866d6ed898336da1b9f836artist[0]".$tag['tags_html']['id3v1']['artist'][0]."methodtrack.scrobblesk".$lastfm_session."timestamp[0]".$timestamp."track[0]".$tag['tags_html']['id3v1']['title'][0]."bea5b2dcd02de18c1b77d48fba9e206c");

			//On envoit la requête
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"http://ws.audioscrobbler.com/2.0/?method=track.scrobble");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
			            "artist[0]=".$tag['tags_html']['id3v1']['artist'][0]."&track[0]=".$tag['tags_html']['id3v1']['title'][0]."&timestamp[0]=".$timestamp."&api_key=b00af6e57f866d6ed898336da1b9f836&api_sig=".$api_sig."&sk=".$lastfm_session);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$server_output = curl_exec ($ch);

			curl_close ($ch);

			return $server_output;
		}
		else{return false;}
	}
?>