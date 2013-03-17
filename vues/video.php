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

							echo '<li><i class="icon-file"></i><a href="'.MODELE_PATH.'media.php?file='.urlencode($arbo).'&u='.$_SESSION['name'].'&p='.$_SESSION['pass'].'" onclick="$(\'#videoPlayer embed\').attr(\'target\', \''.MODELE_PATH.'media.php?file='.urlencode($arbo).'&u='.$_SESSION['name'].'&p='.$_SESSION['pass'].'\');ht=$(\'#videoPlayer\').html();$(\'#videoPlayer\').html(ht);return false;">'.$file_name.'</a></li>';
						}
					}
					echo '</ul>';
				}

				foreach($arborescence as $arbo){
					showArborescence($arbo); //On affiche l'arborescence
				}
			?>	
		</div>

		<div class="span8">
			<div class="separator"></div>
			<div id="videoPlayer">
				<embed type="application/x-vlc-plugin" name="video1" autoplay="yes" loop="no" width="100%" height="80%" target="" />
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.onload=function(){
		//On affiche uniquement la racine
		$('#mediaList ul').css('display', 'none');
		$('#mediaList li').css('display', 'none');
		$('#mediaList > ul').css('display', '');

		/* 
		Cache ou affiche le contenu d'un dossier
		@params	element	Dossier cliqué
		*/
		function toggleChild(element){
			if(element.children('ul').css('display') == 'none' || element.children('li').css('display') == 'none'){
				//On affiche le contenu
				element.children('.addToLibrary').children('.icon-folder-close').attr('class', 'icon-folder-open');
				element.children('ul').css('display', '');
				element.children('li').css('display', '');
			}
			else{
				//On cache le contenu
				element.children('.addToLibrary').children('.icon-folder-open').attr('class', 'icon-folder-close');
				element.children('ul').css('display', 'none');
				element.children('li').css('display', 'none');
			}
		}

		//On attache les évènement aux dossier
		$('#mediaList .addToLibrary').each(function(){
			$(this).click(function(){
				toggleChild($(this).parent('ul'));
				return false;
			});
		});
	};
</script>