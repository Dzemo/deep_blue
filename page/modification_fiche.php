<div class="titre">Modification de la fiche de sécurité</div>

<?php

	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."printFormFicheSecurite.php");

	if(isset($_GET['id'])){
		$id_fiche = intval($_GET['id']);
		
		$fiche = FicheSecuriteDao::getById($id_fiche);

		if($fiche != null){
			if($fiche->getEtat() == FicheSecurite::etatArchive)
			echo "<div class='sous-titre'>La fiche ".$id_fiche." est archivée</div>";
			else
				printFormFicheSecurite('index.php?page=consulter_fiche&id='.$id_fiche, $fiche);
		}
		else{
			echo "<br>La fiche ".$id_fiche." n'existe pas";
		}
	}
	else{
		//echo "<br>Aucune fiche de sélectionnée";
		header('Location: '.$GLOBALS['dns'].'index.php');
	}
?>