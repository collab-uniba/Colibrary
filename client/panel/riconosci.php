<?php
	$password = $_GET["password"];
	@$xml = simplexml_load_file("conf.xml");
	$passxml = $xml->password;
	
	if ($password == $passxml) { 
		session_start();
		$_SESSION["password"] = $password;
		header("Location: main.php");		
	}
	else header("Location: index.php?errore=1");
?>