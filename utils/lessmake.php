<?php
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."config.php");
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."utils".DIRECTORY_SEPARATOR."less.inc.php");

	$lessc = new lessc;
	$lessc->setPreserveComments(true);

	try{
		if(isset($force_css_compile) && $force_css_compile == true){
			$lessc->compileFile("css/styles.less","css/styles.css");
		}
		else{
			$lessc->checkedCompile("css/styles.less","css/styles.css");	
		}
	}catch(exception $e){
		echo "Erreur less : " . $e->getMessage();
	}
?>