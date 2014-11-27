<?php

	$val = "á|â|à|å|ä ð|é|ê|è|ë í|î|ì|ï ó|ô|ò|ø|õ|ö ú|û|ù|ü æ ç ß abc ABC 123";



	$string = "ë.Buffière";//á|â|à|å|ä ð|é|ê|è|ë í|î|ì|ï ó|ô|ò|ø|õ|ö ú|û|ù|ü æ ç ß abc ABC 123"; // my definition for string variable
	$accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig|ring|h|H|slash|tilde);/';

	$string_encoded = htmlentities($string,ENT_NOQUOTES,'UTF-8');

	$string_ok = preg_replace($accents,'$1',$string_encoded);

	echo $string_encoded.'<br>';
	echo $string_ok.'<br>';
?>