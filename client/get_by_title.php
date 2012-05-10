<?php

	header("Content-Type: text/xml");
	
	set_time_limit(0);


	// titolo inserito nel form con doppi apici avanti e dietro
	$name = $_GET['testo'];
	$new_code = str_replace(" ","+",$name);
	$new_code = '"'.$new_code.'"';
	 

	 
	 // funzioni per recuperare i dati da bibsonomy data la query utente
	 $user='antogrim2';
     $pass='260aa1fa60ef2bebec3c7092bbc15c3e';
     $url = 'http://bibsonomy.org/api/posts?search='.$new_code.'&resourcetype=bibtex';
	 $ch = curl_init($url);
     //curl_setopt($ch, CURLOPT_GET,1);
     curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
     curl_setopt($ch, CURLOPT_HEADER,0);
     curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
     curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
     $query=curl_exec($ch);
	
	sleep(2); //wait 2 seconds to avoid being throttled by Bibsonomy API (only 1 query per second allowed)
	$xml = simplexml_load_string($query);
	
	$hint = "";
    $count=0;
    $title=array(); //necessario per tenere traccia delle publicazioni pluritaggate che altrimenti sarebbero ripetute
	$instances=$xml->posts['end'];
	 
	 
   foreach($xml->posts->post as $item){
    if (stripos(htmlspecialchars($item->bibtex['title']),$name)===false) {}  // verifico che la query utente sia effettivamente risolta 
	  else if ($count<10 && !in_array(htmlspecialchars($item->bibtex['title']),$title) ){
		  $hint .= "<publication>";	
		  $hint .= "<n>" . $count. "</n>";
		  $hint .= "<entry_type>" . htmlspecialchars($item->bibtex['entrytype']) . "</entry_type>";
		  $hint .= "<book_title>" . htmlspecialchars($item->bibtex['booktitle']) . "</book_title>";
		  $hint .= "<doi_isbn>" . htmlspecialchars($item->bibtex['misc']) . "</doi_isbn>";
		  $hint .= "<author>" . htmlspecialchars($item->bibtex['author']) . "</author>";
		  $hint .= "<title>" . htmlspecialchars($item->bibtex['title']) . "</title>";
		  $hint .= "<journal>" . htmlspecialchars($item->bibtex['journal']) . "</journal>";
		  $hint .= "<publisher>" . htmlspecialchars($item->bibtex['publisher']) . "</publisher>";
		  $hint .= "<year>" . htmlspecialchars($item->bibtex['year']) . "</year>";
		  $hint .= "<url>" . htmlspecialchars($item->bibtex['url']) . "</url>";
    	  $hint .= "<interhash>" . htmlspecialchars($item->bibtex['interhash']) . "</interhash>";
		  $hint .= "</publication>";
    	  array_push($title,htmlspecialchars($item->bibtex['title']));
		  $count++;
	     
		}
   
 }
	if ($hint == "") $response = "<publication><doi_isbn>No Publications found!</doi_isbn></publication>";
	else $response = $hint;
	
	$response = "<?xml version=\"1.0\" ?><publications><n_of_instances>".$instances."</n_of_instances>" . $response . "</publications>";
	echo $response;	
?>
