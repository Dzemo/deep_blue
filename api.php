<?php
	require_once("session.php");
	require_once("utils/DateStringUtils.php");

	//All echo are catch to be output in a log file
	ob_start();

	$date = new DateTime();
	$date->setTimestamp(time());
	$date->format("l j M G:i:s");

	echo $date->format("l j M G:i:s")."\n\n";

	echo "POST ";
	var_dump($_POST);

	if(isset($_POST['utilisateur_login']) && strlen($_POST['utilisateur_login']) > 0){
		$utilisateur_login = base64_decode(filter_var($_POST['utilisateur_login'], FILTER_SANITIZE_STRING));

		echo "\nLogin: '$utilisateur_login'\n";
	}
	else{
		echo "\nLogin: invalide\n";
	}

	if(isset($_POST['utilisateur_mot_de_passe']) && strlen($_POST['utilisateur_mot_de_passe']) > 0){
		$utilisateur_mot_de_passe = base64_decode(filter_var($_POST['utilisateur_mot_de_passe'], FILTER_SANITIZE_STRING));

		echo "\nMdp: '$utilisateur_mot_de_passe'\n";
	}
	else{
		echo "\nMdp: invalide\n";
	}


	$fiches = FicheSecuriteDao::getAll();
	$fichesJSON = json_encode((array)$fiches);

	echo $fichesJSON;

	$output = ob_get_contents();
	ob_clean();

	file_put_contents("log/api_log.txt", $output);
	
	echo $fichesJSON;
?>