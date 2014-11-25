<div class="titre">Modification de la fiche de sécurité</div>

<?php

	require_once($_SERVER['DOCUMENT_ROOT']."/deep_blue/utils/printFormFicheSecurite.php");

	if(isset($_GET['id'])){
		$id_fiche = intval($_GET['id']);
		
		$fiche = FicheSecuriteDao::getById($id_fiche);

		if($fiche != null){
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