<?php
	function getThumbnail($link){
		$file = file_get_contents($link);
		$start = "registerImage(\"";
		$file = substr($file, strpos($file, $start), strlen($file) - strpos($file, $start));
		
		$file = substr($file, 33, strlen($file) - strpos($file, $start));
		$file = substr($file, 0, strpos($file, "\""));
		$file = trim($file);
		return $file;
	}
	
	echo getThumbnail($_GET["url"]);
?>