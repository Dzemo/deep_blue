<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/deep_blue/config.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/deep_blue/utils/less.inc.php");

	$lessc = new lessc;

	try{
		if(isset($force_css_compile) && $force_css_compile == true){
			$lessc->setPreserveComments(true);
			$lessc->compileFile("css/styles.less","css/styles.css");
		}
		else{
			$lessc->checkedCompile("css/styles.less","css/styles.css");	
		}
	}catch(exception $e){
		echo "Erreur less : " . $e->getMessage();
	}
?>