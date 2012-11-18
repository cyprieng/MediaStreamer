<div class="container-fluid">
	<div class="row-fluid">
		<div class="span5" id="mediaList">
			<?php
				//On affiche le nom du dossier
				echo '<ul><i class="icon-folder-close"></i><a href="#">Podcast</a><br/>';
				
				//Affiche l'arboresence des podcast (see modeles/podcast.php)
				foreach($podcast as $arbo){ //Podcast
					foreach($arbo as $id => $pod){ //Elt d'un podcast
						if($id == 0){ //Titre du podcast
							echo '<ul><i class="icon-folder-close"></i><a href="#">'.substr(strrchr($pod,'/'),1).'</a><br/>';
						}
						else{ //Piste du podcast
							echo '<li><i class="icon-file"></i><a href="'.MODELE_PATH.'media.php?file='.$pod[0].'" onclick="$(\'#playlistPodcast\').append(\'<li class=\\\'ui-state-default\\\' src=\\\''.MODELE_PATH.'media.php?file='.urlencode($pod[0]).'\\\'>'.addslashes(preg_replace('#"#', '\'\'', Trim($pod[1]))).'</li>\');trackInit();return false;">'.Trim($pod[1]).'</a></li>';
						}
					}
					echo '</ul>';
				}
				echo '</ul>';
			?>	
		</div>
		<div class="span5">
		 	<audio id="player" src="" controls></audio><br/>
		 	<a class="btn btn-small" href="#"><i class="icon-fast-backward"></i></a>
			<a class="btn btn-small" href="#"><i class="icon-fast-forward"></i></a>
			<a class="btn btn-small" href="#" onclick="random();"><i class="icon-random"></i></a>
			<a class="btn btn-small" href="#" onclick="$(this).toggleClass('active');"><i class="icon-repeat"></i></a>

		 	<br/><br/>
		 	<h3>playlist</h3>
		 	<ul id="playlistPodcast">
			</ul>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.onload=function(){
		//On affiche uniquement la racine
		$('#mediaList ul').css('display', 'none');
		$('#mediaList li').css('display', 'none');
		$('#mediaList ul:eq(0)').css('display', '');

		//playlistPodcast sortable cf jqueryui
		$('#playlistPodcast').sortable({
			placeholder: "ui-state-highlight"
		});
		$('#playlistPodcast').disableSelection();

		/* 
		Affiche le contenu d'un dossier
		@params	element	Dossier cliqué
		*/
		function showChild(element){
			//On affiche le contenu
			element.children('.icon-folder-close').attr('class', 'icon-folder-open');
			element.children('ul').css('display', '');
			element.children('li').css('display', '');

			//Lors du prochain clic on cache
			element.click(function(){ 
				hideChild($(this));
				return false;
			});
		}

		/* 
		Cache le contenu d'un dossier
		@params	element	Dossier cliqué
		*/
		function hideChild(element){
			//On cache le contenu
			element.children('.icon-folder-open').attr('class', 'icon-folder-close');
			element.children('ul').css('display', 'none');
			element.children('li').css('display', 'none');

			//Lors du prochain clic on affiche
			element.click(function(){
				showChild($(this));
				return false;
			});
		}

		//On attache les évènement aux dossier
		$('#mediaList ul').each(function(){
			$(this).click(function(){
				showChild($(this));

				return false;
			});
		});

		//On le supprime pour les liens
		$('#mediaList li').each(function(){
			$(this).click(function(){
				return false;
			});
		});

		/*
		Lit la chanson
		@params	id	Numéro de la chanson dans la playlistPodcast
		*/
		function play(id){
			$('#player').attr('src', $('#playlistPodcast li:eq('+id+')').attr('src')); //On récupère le lien
			$('#playlistPodcast li i').remove();
			$('#playlistPodcast li:eq('+id+')').append('<i class="icon-music"></i>'); //On ajoute l'icon
			$('#player')[0].play(); //On lit
		}

		/* 
		Passe à la chanson suivante ou démarre la playlistPodcast autmatiquement
		*/
		function playlistPodcast(){
			if($('#player').attr('src') == ''){ //Aucune piste en lecture
				if($('#playlistPodcast li:eq(0)').length){ //playlistPodcast non vide
					play(0); //On commence la lexture
				}
			}
			else{
				if($('#player')[0].currentTime == $('#player')[0].duration){ //Piste fini => on passe à la suivante
					nextTrack();
				}
			}			
			setTimeout(playlistPodcast, 1000);
		}
		playlistPodcast();

		/* 
		Passe à la chason suivante de la playlistPodcast
		*/
		function nextTrack(){
			for(var i=0;i<=$('#playlistPodcast li').length;i++){
				//On récupère la piste actuel
				if($('#playlistPodcast li:eq('+i+')').attr('src') == $('#player').attr('src') && $('#playlistPodcast li:eq('+i+') .icon-music').length){
					var curentPlay = i+1;
				}
			}

			if($('#playlistPodcast li:eq('+curentPlay+')').length){ //On lit la piste suivante
				play(curentPlay);
			}
			else if($('.icon-repeat').parent('a').hasClass('active')){ //Si repeat => on retourne au début
				play(0);
			}
			else{ //Sion => stop
				$('#player').attr('src', 'stop');
				$('#playlistPodcast li i').remove();
			}
		}

		/* 
		Passe à la chason précédente de la playlistPodcast
		*/
		function previousTrack(){
			//On récupère la piste actuel
			for(var i=0;i<=$('#playlistPodcast li').length;i++){
				if($('#playlistPodcast li:eq('+i+')').attr('src') == $('#player').attr('src') && $('#playlistPodcast li:eq('+i+') .icon-music').length){
					var curentPlay = i-1;
				}
			}

			play(curentPlay);
		}

		//On affecte les actions aux boutons suivants/précédents
		$('.span5:eq(1) a:eq(0)').click(previousTrack);
		$('.span5:eq(1) a:eq(1)').click(nextTrack);

		/*
		Ajoute le contenu d'un dossier à la playlistPodcast
		*/
		function addFolderToplaylistPodcast(){
			$('#mediaList ul').each(function(){ //On boucle les dossier
				$(this).mouseenter(function(){ //Mouse enter => bouton
					$(this).children('a:eq(0)').after('<i class="icon-play-circle"></i>');

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
		addFolderToplaylistPodcast();
	};

	/*
	On initialise la piste lors de l'ajout
	*/
	function trackInit(){
		$('#playlistPodcast li').each(function(){
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
				$('#playlistPodcast li i').remove();
				$(this).append('<i class="icon-music"></i>');
				$('#player')[0].play();
			});
		});
	}

	/*
	Mélange la playlit
	*/
	function random(){
		var random;
		var number = [];
		var playlistPodcast = [];
		var playlistPodcastLength = $('#playlistPodcast li').length-1;

		//On récupère la playlistPodcast
		for(var i=0;i<=playlistPodcastLength;i++){
			playlistPodcast[i] = {};
			playlistPodcast[i]['text'] = $('#playlistPodcast li:eq('+i+')').text();
			playlistPodcast[i]['src'] = $('#playlistPodcast li:eq('+i+')').attr('src');
			
			if($('#playlistPodcast li:eq('+i+')').attr('src') == $('#player').attr('src') && $('#playlistPodcast li:eq('+i+') .icon-music').length){
				playlistPodcast[i]['play'] = true;
			}
			else{playlistPodcast[i]['play'] = false;}
		}

		$('#playlistPodcast li').remove(); //On supprime la playlistPodcast

		//On affiche la playlistPodcast au hasard
		i=0;var playing;
		while(i<=playlistPodcastLength){
			random = Math.floor( Math.random() * (playlistPodcastLength - 0 + 1) ) + 0;

			if($.inArray(random, number) == -1){
				playing = (playlistPodcast[random]['play'])? '<i class="icon-music"></i>':'';
				$('#playlistPodcast').append("<li class='ui-state-default' src='"+playlistPodcast[random]['src']+"'>"+playlistPodcast[random]['text']+ playing +"</li>");

				number[number.length] = random;
				i++;
			}
		}

		trackInit();
	}
</script>