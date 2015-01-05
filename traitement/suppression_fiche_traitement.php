<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."session.php");
if($connecte){
	if(isset($_GET['id'])){
		$id_fiche = intval($_GET['id']);
		
		$fiche = FicheSecuriteDao::getById($id_fiche);

		if($fiche != null){
			$fiche = FicheSecuriteDao::delete($fiche);
			header('Location: '.$GLOBALS['dns'].'index.php?page=liste_fiches');
		}
		else{
			header('Location: '.$GLOBALS['dns'].'index.php');
		}
	}
	else{
		header('Location: '.$GLOBALS['dns'].'index.php');
	}
}
else{
	header('Location: '.$GLOBALS['dns'].'index.php');
}
	
?>
