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
					echo '<ul><i class="icon-folder-close"></i><a href="#">'.substr(strrchr($arborescence[0],'/'),1).'</a><br/>';
					unset($arborescence[0]);
					
					foreach($arborescence as $arbo){
						if(is_array($arbo)){ //C'est un dossier => on le parcours
							showArborescence($arbo);
						}
						else{ //Fichier => on l'affiche
							echo '<li><i class="icon-file"></i><a href="'.MODELE_PATH.'media.php?file='.$arbo.'" onclick="player.play(\''.MODELE_PATH.'media.php?file='.$arbo.'\');return false;">'.substr(strrchr($arbo,'/'),1).'</a></li>';
						}
					}
					echo '</ul>';
				}

				showArborescence($arborescence); //On affiche l'arborescence
			?>	
		</div>

		<div class="span6">
			<div id="vlc1"></div>
		</div>
	</div>
</div>

 
<script language="javascript" src="http://revolunet.github.com/VLCcontrols/src/jquery-vlc.js"></script> 
<link rel="stylesheet" type="text/css" href="http://revolunet.github.com/VLCcontrols/src/styles.css" /> 

<script type="text/javascript">
	var player;
	
	window.onload=function(){
		player = VLCobject.embedPlayer('vlc1', 400, 300); //On créé le lecteur VLC

		//On affiche uniquement la racine
		$('#mediaList ul').css('display', 'none');
		$('#mediaList li').css('display', 'none');
		$('#mediaList ul:eq(0)').css('display', '');

		/* 
		Affiche le contenu d'un dossier
		@params	element	Dossier cliqué
		*/
		function showChild(element){
			//On affiche le contenu
			element.children('i').attr('class', 'icon-folder-open');
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
			element.children('i').attr('class', 'icon-folder-close');
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
	};
</script>