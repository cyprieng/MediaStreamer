<div class="row-fluid" id="player-container">
	<div class="span8 offset1">
		<audio id="player" src="" controls></audio>
	</div>
	<div class="span2">
		<a class="btn btn-small" href="#"><i class="icon-fast-backward"></i></a>
		<a class="btn btn-small" href="#"><i class="icon-fast-forward"></i></a>
		<a class="btn btn-small" href="#" onclick="random();"><i class="icon-random"></i></a>
		<a class="btn btn-small" href="#" onclick="$(this).toggleClass('active');"><i class="icon-repeat"></i></a>
	</div>
</div>
<br/><br/><br/><br/>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4" id="mediaList">
			<?php
				/* 
				Affiche l'arboresence
				@params	arborescence	Array d'arborescence
				@return	Affiche une mise en forme l'arborescence
				@see	modele/home.php
				*/
				function showArborescence($arborescence){
					//On affiche le nom du dossier
					if(!isset($arborescence[0])) return;
					echo '<ul><span class="addToLibrary"><i class="icon-folder-close"></i><a href="#">'.substr(strrchr($arborescence[0],'/'),1).'</a></span><br/>';
					unset($arborescence[0]);
					
					foreach($arborescence as $arbo){
						if(is_array($arbo)){ //C'est un dossier => on le parcours
							showArborescence($arbo);
						}
						else{ //Fichier => on l'affiche
							$info = pathinfo($arbo);
							$file_name = basename($arbo,'.'.$info['extension']);

							echo '<li><i class="icon-file"></i><a href="'.MODELE_PATH.'media.php?file='.$arbo.'" onclick="$(\'#playlist\').append(\'<li class=\\\'ui-state-default\\\' src=\\\''.MODELE_PATH.'media.php?file='.urlencode($arbo).'\\\'>'.addslashes($file_name).'</li>\');trackInit();return false;">'.$file_name.'</a></li>';
						}
					}
					echo '</ul>';
				}

				foreach($arborescence as $arbo){
					showArborescence($arbo); //On affiche l'arborescence
				}
			?>	
		</div>
		<div class="span4">
			<div class="separator"></div>
		 	<h3>Playlist</h3>
		 	<ul id="playlist">
			</ul>
		</div>
		<div class="span4" id="lyrics">
			<div class="separator"></div>
		 	<h3>Lyrics</h3>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.onload=function(){
		//On affiche uniquement la racine
		$('#mediaList ul').css('display', 'none');
		$('#mediaList li').css('display', 'none');
		$('#mediaList > ul').css('display', '');

		//Playlist sortable cf jqueryui
		$('#playlist').sortable();
		$('#playlist').disableSelection();

		/* 
		Affiche le contenu d'un dossier
		@params	element	Dossier cliqué
		*/
		function showChild(element){
			//On affiche le contenu
			element.children('.addToLibrary').children('.icon-folder-close').attr('class', 'icon-folder-open');
			element.children('ul').css('display', '');
			element.children('li').css('display', '');

			//Lors du prochain clic on cache
			element.children('.addToLibrary').click(function(){ 
				hideChild($(this).parent('ul'));
				return false;
			});
		}

		/* 
		Cache le contenu d'un dossier
		@params	element	Dossier cliqué
		*/
		function hideChild(element){
			//On cache le contenu
			element.children('.addToLibrary').children('.icon-folder-open').attr('class', 'icon-folder-close');
			element.children('ul').css('display', 'none');
			element.children('li').css('display', 'none');

			//Lors du prochain clic on affiche
			element.children('.addToLibrary').click(function(){
				showChild($(this).parent('ul'));
				return false;
			});
		}

		//On attache les évènement aux dossier
		$('#mediaList .addToLibrary').each(function(){
			$(this).click(function(){
				showChild($(this).parent('ul'));

				return false;
			});
		});

		/*
		Lit la chanson
		@params	id	Numéro de la chanson dans la playlist
		*/
		function play(id){
			$('#player').attr('src', $('#playlist li:eq('+id+')').attr('src')); //On récupère le lien
			$('#playlist li i').remove();
			$('#playlist li:eq('+id+')').append('<i class="icon-music"></i>'); //On ajoute l'icon
			$('#player')[0].play(); //On lit

			//On affiche les paroles et on scrobble
			lyrics();
			scrobble();
		}

		/* 
		Passe à la chanson suivante ou démarre la playlist autmatiquement
		*/
		function playlist(){
			if($('#player').attr('src') == ''){ //Aucune piste en lecture
				if($('#playlist li:eq(0)').length){ //Playlist non vide
					play(0); //On commence la lexture
				}
			}
			else{
				if($('#player')[0].currentTime == $('#player')[0].duration){ //Piste fini => on passe à la suivante
					nextTrack();
				}
			}			
			setTimeout(playlist, 1000);
		}
		playlist();

		/* 
		Passe à la chason suivante de la playlist
		*/
		function nextTrack(){
			for(var i=0;i<=$('#playlist li').length;i++){
				//On récupère la piste actuel
				if($('#playlist li:eq('+i+')').attr('src') == $('#player').attr('src') && $('#playlist li:eq('+i+') .icon-music').length){
					var curentPlay = i+1;
				}
			}

			if($('#playlist li:eq('+curentPlay+')').length){ //On lit la piste suivante
				play(curentPlay);
			}
			else if($('.icon-repeat').parent('a').hasClass('active')){ //Si repeat => on retourne au début
				play(0);
			}
			else{ //Sion => stop
				$('#player').attr('src', 'stop');
				$('#playlist li i').remove();
			}
		}

		/* 
		Passe à la chason précédente de la playlist
		*/
		function previousTrack(){
			//On récupère la piste actuel
			for(var i=0;i<=$('#playlist li').length;i++){
				if($('#playlist li:eq('+i+')').attr('src') == $('#player').attr('src') && $('#playlist li:eq('+i+') .icon-music').length){
					var curentPlay = i-1;
				}
			}

			play(curentPlay);
		}

		//On affecte les actions aux boutons suivants/précédents
		$('#player-container a:eq(0)').click(previousTrack);
		$('#player-container a:eq(1)').click(nextTrack);

		/*
		Ajoute le contenu d'un dossier à la playlist
		*/
		function addFolderToPlaylist(){
			$('#mediaList ul').each(function(){ //On boucle les dossier
				$(this).mouseenter(function(){ //Mouse enter => bouton
					$(this).children('.addToLibrary').after('<i class="icon-play-circle"></i>');

					$('.icon-play-circle').click(function(){ //On ajoute le contenu du dossier
						//On déclenche le click de toutes les pistes du dossier
						$(this).parent('ul').children('li').children('a').each(function(){
							$(this).click();
						});

						//On déclenche le click de tuotes les pistes des sous-dossier
						var folder = $(this).parent('ul').children('ul');
						while(folder.length){
							folder.children('li').children('a').each(function(){
								$(this).click();
							});

							folder = folder.children('ul');
						}
					});
				});
				$(this).mouseleave(function(){ //Mouse leave => supprime bouton
					$(this).children('.icon-play-circle').remove();
				});
			});
		}
		addFolderToPlaylist();
	};

	/* 
	Scrobble la musique sur lastfm si 50% s'est écoulé
	*/
	function scrobble(){
		if($('#player').attr('src') != ''){ //Une chanson est en lecture
			if($('#player')[0].currentTime / $('#player')[0].duration >= 0.5){ //50% s'est écoulé => on scrobble
				//On récupère la musique
				var file = $('#player').attr('src');
				file = file.replace('<?php echo MODELE_PATH; ?>media.php?file=', '');

				//on appel le script php
				$.ajax({
					type: 'GET',
					url: '<?php echo ROOT_PATH_HTML; ?>index.php',
					data: {
						p : 'scrobbler',
						file : file,
					},
				});
			}
			else{
				//On relance la fonction
				setTimeout(scrobble, 5000);
			}
		}
		else{
			//On relance la fonction
			setTimeout(scrobble, 5000);
		}
	}

	/*
	Affiche les paroles de la piste en cours
	*/
	function lyrics(){
		$('#lyrics pre').remove();

		//On récupère le ficier
		var file = $('#player').attr('src');
		file = file.replace('<?php echo MODELE_PATH; ?>media.php?file=', '');

		//on appel le script php
		$.ajax({
			type: 'GET',
			url: '<?php echo ROOT_PATH_HTML; ?>index.php',
			data: {
				p : 'lyrics',
				file : file,
			},

			success: function(data, textStatus, jqXHR){
				$('#lyrics pre').remove();

				//On récupère et on affiche les lyrics
				$('#lyrics').append('<pre style="display:none;">'+data+'</pre>');
				var lyrics = $('#lyrics lyrics').html();
				$('#lyrics pre').html(lyrics).css('display', '');
				var i=1;
				$('#lyrics pre br').each(function(){
					if(i%2 == 0){
						$(this).remove();
					}
					i++;
				});
			}
		});
	}

	/*
	On initialise la piste lors de l'ajout
	*/
	function trackInit(){
		$('#playlist li').each(function(){
			$(this).unbind('mouseenter').unbind('mouseleave').unbind('dblclick');

			//Mpouse enter => bouton suppression
			$(this).mouseenter(function(){
				$(this).children(".icon-remove").remove();
				$(this).append('<i class="icon-remove"></i>');
				$(this).children(".icon-remove").click(function(){
					$(this).parent().remove();
				});
			});
			$(this).mouseleave(function(){
				$(this).children(".icon-remove").remove();
			});

			//Double clique => play
			$(this).dblclick(function(){
				$('#player').attr('src', $(this).attr('src'));
				$('#playlist li i').remove();
				$(this).append('<i class="icon-music"></i>');
				$('#player')[0].play();

				lyrics();
				scrobble();
			});
		});
	}

	/*
	Mélange la playlit
	*/
	function random(){
		var random;
		var number = [];
		var playlist = [];
		var playlistLength = $('#playlist li').length-1;

		//On récupère la playlist
		for(var i=0;i<=playlistLength;i++){
			playlist[i] = {};
			playlist[i]['text'] = $('#playlist li:eq('+i+')').text();
			playlist[i]['src'] = $('#playlist li:eq('+i+')').attr('src');
			
			if($('#playlist li:eq('+i+')').attr('src') == $('#player').attr('src') && $('#playlist li:eq('+i+') .icon-music').length){
				playlist[i]['play'] = true;
			}
			else{playlist[i]['play'] = false;}
		}

		$('#playlist li').remove(); //On supprime la playlist

		//On affiche la playlist au hasard
		i=0;var playing;
		while(i<=playlistLength){
			random = Math.floor( Math.random() * (playlistLength - 0 + 1) ) + 0;

			if($.inArray(random, number) == -1){
				playing = (playlist[random]['play'])? '<i class="icon-music"></i>':'';
				$('#playlist').append("<li class='ui-state-default' src='"+playlist[random]['src']+"'>"+playlist[random]['text']+ playing +"</li>");

				number[number.length] = random;
				i++;
			}
		}

		trackInit();
	}
</script>