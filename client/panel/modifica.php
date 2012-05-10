<?php

	session_start();
	if (!$_SESSION["password"]) header("Location: index.php");


	$get_password = $_GET["password"];
	$get_delayMessage = $_GET["delayMessage"];
	$get_delaySuggest = $_GET["delaySuggest"];	
	

	// create doctype
	$dom = new DOMDocument("1.0");
		
	// create root element
	$root = $dom->createElement("configurazione");
	$dom->appendChild($root);
	
	// create child element
	$password = $dom->createElement("password");
	$root->appendChild($password);
	$delaySuggest = $dom->createElement("delaySuggest");
	$root->appendChild($delaySuggest);
	$delayMessage = $dom->createElement("delayMessage");
	$root->appendChild($delayMessage);
	
	
	// create text node
	$text = $dom->createTextNode($get_password);
	$password->appendChild($text);
	$text = $dom->createTextNode($get_delayMessage);
	$delayMessage->appendChild($text);
	$text = $dom->createTextNode($get_delaySuggest);
	$delaySuggest->appendChild($text);	
	
	
	// save and display tree
	$dom->save("conf.xml");


	header("Location: main.php?fatto=1");
?> 
