<?php
// SEZIONE GET TAGS DAL PORTALE "LIBRARYTHING.COM"
function getReviewsAnobii($isbn) {

	$anobii_bookcode = ISBN_to_ANOBII_ID($isbn);
	
	$url = "http://www.anobii.com/books/$anobii_bookcode/comments/";
	$ch = curl_init($url);
	$agent = '';
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_COOKIE, "home_view=0");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	$page = curl_exec($ch);
	curl_close($ch);

	// facciamo partire l'analizi da questo blocco
	$pos_to_start_page_parsing = strpos($page, '<ul class="comment_block">');
	$page= substr($page, $pos_to_start_page_parsing);

	$i = 0;
	$reviews_found = 0;
	while (strpos($page, 'comment_block')) {
		$to_analize = $page;
		$pos 		= strpos($to_analize, 'comment_block');
		$to_analize = substr($to_analize, $pos+10);		
		//cerchiamo la posizione del commento successivo (se esiste) per restringere il campo di ricerca al commento attualmente analizzato		
		$next_pos	= strpos($to_analize, 'comment_block');
		// SE esiste un commento successivo, $to_analize lo restringiamo in coda, altrimenti resta così com'è, cioè contiene solo il commento attualmente analizzato
		if ($next_pos)	
			$to_analize = substr($to_analize, 0, $next_pos);		// corpo del testo da analizzare adesso
		// _______________________________________prima calcoliamo di quante stelle è composto il commento
		$string_stars 	= $to_analize;
		$pos 			= strpos($string_stars, '<span class="stars">');
		$string_stars 	= substr($string_stars, $pos);
		$pos 			= strpos($string_stars, '</span>');
		$string_stars 	= substr($string_stars, 0, $pos+7);						//	<span...> <img...>....<img...></span>	(ora contiamo le img caricate)
		$stars_found=0;															//	(ora contiamo le img caricate)
		while (strpos($string_stars, '<img')) {
			$pos = strpos($string_stars, '<img');
			$string_stars = substr($string_stars, $pos+4);
			$stars_found++;
		}
		$stars[$i] = $stars_found;
		// _________________________________________________________________catturiamo la review
		$string_review 	= $to_analize;
		if (strpos($string_review, '<span id="comment_person_more'))	
			$pos = strpos($string_review, '<span id="comment_person_more');
		else															
			$pos = strpos($string_review, '<span id="comment_person_');
		$string_review 	= substr($string_review, $pos);
		$pos 			= strpos($string_review, '</span>');
		$string_review 	= substr($string_review, 0, $pos+7);
		$review[$i] = DeleteTagsFromString($string_review);
		// _________________________________________________________catturiamo l'autore della review
		$string_author	= $to_analize;
		$pos			= strpos($string_author, '<li class="comment_details">');
		$string_author	= substr($string_author, $pos);						//	<a href="/jikoaite/books" title="Vedi la libreria di Jikoaite">Jikoaite</a>
		$pos			= strpos($string_author , 'title=');				
		$string_author	= substr($string_author, $pos);						//	title="Vedi la libreria di Jikoaite">Jikoaite</a>
		$pos			= strpos($string_author , '>');
		$string_author	= substr($string_author, $pos+1);					//	Jikoaite</a>
		$pos			= strpos($string_author , '<');
		$string_author	= substr($string_author, 0, $pos);					//	Jikoaite
		$author[$i] = $string_author;
		$i++;
		// scorriamo il testo da analizzare
		$pos 		= strpos($page, 'comment_block');
		$page		= substr($page, $pos+1);
	}
	$reviews_found = $i;
	// FASE DI PREPARAZIONE XML OUTPUT
	$_xml  ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
	$_xml .= "<result>\r\n";
	for ($i=0;$i<$reviews_found;$i++) {
		$_xml .="\t<isbn>$isbn</isbn>\r\n";
		$_xml .="\t<tag/>\r\n";
		$_xml .="\t<occurrences/>\r\n";
		$_xml .="\t<user>$author[$i]</user>\r\n";
		$_xml .="\t<review>$review[$i]</review>\r\n";	
		$_xml .="\t<rating>$stars[$i]</rating>\r\n";
		$_xml .="\t<minrating>1</minrating>\r\n";
		$_xml .="\t<maxrating>4</maxrating>\r\n";
		//$_xml .="\t<imgrating>$img[$i]</imgrating>\r\n";
		$_xml .="\t<imgrating></imgrating>\r\n";
	}
	$_xml.= "</result>";
	$_xml = str_replace ("&", "&amp;", $_xml);
	// FASE DI SALVATAGGIO FILE XML
	$path='_repositoryXML/';
	$file=$path.$isbn.'_reviews_anobii.xml';
	$fp= fopen($file, "w");
	fwrite($fp, $_xml);
	fclose($fp);
	
	return FixEncoding($_xml);
}
?>