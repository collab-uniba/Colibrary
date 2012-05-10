<?php
	session_start();
	$dirname = "../../rdf/";
	$handle = opendir($dirname);
	while (false !== ($file = readdir($handle))) { 
		if(is_file($dirname.$file)){
			unlink($dirname.$file);
		}
	}
	$handle = closedir($handle);
	header("Location: main.php?fatto=2");

?>