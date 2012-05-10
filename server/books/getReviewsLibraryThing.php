<?php
// SEZIONE GET TAGS DAL PORTALE "LIBRARYTHING.COM"
function getReviewsLibraryThing($isbn) {
	$lt_bookcode =  ISBN_to_LT_WORK($isbn);

	$url  = "http://www.librarything.com/ajaxinc_bookreviews.php?print=1&work=".$lt_bookcode;

	$reviews_found = 0;
	$page = file_get_contents($url);

	while (strpos($page, 'div class="bookReview"')) {
		$pos = strpos($page, '<div id=');
		$page=substr($page, $pos); 
		
		// calcoliamo il rating della review
		$pos = strpos($page, 'div class="bookReview"');
		$page_rating=substr($page, 0, $pos); 
		if (strpos($page_rating, '/ss10.gif'))		$rate[$reviews_found] = "5";
		else if (strpos($page_rating, '/ss9.gif'))	$rate[$reviews_found] = "4.5";
		else if (strpos($page_rating, '/ss8.gif'))	$rate[$reviews_found] = "4";
		else if (strpos($page_rating, '/ss7.gif'))	$rate[$reviews_found] = "3.5";
		else if (strpos($page_rating, '/ss6.gif'))	$rate[$reviews_found] = "3";
		else if (strpos($page_rating, '/ss5.gif'))	$rate[$reviews_found] = "2.5";
		else if (strpos($page_rating, '/ss4.gif'))	$rate[$reviews_found] = "2";
		else if (strpos($page_rating, '/ss3.gif'))	$rate[$reviews_found] = "1.5";
		else if (strpos($page_rating, '/ss2.gif'))	$rate[$reviews_found] = "1";
		else 										$rate[$reviews_found] = "0.5";

		// estraiamo i link all'immagine
		if (strpos($page_rating, '/ss10.gif'))		$img[$reviews_found] = "http://www.librarything.com/pics/ss10.gif";
		else if (strpos($page_rating, '/ss9.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss9.gif";
		else if (strpos($page_rating, '/ss8.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss8.gif";
		else if (strpos($page_rating, '/ss7.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss7.gif";
		else if (strpos($page_rating, '/ss6.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss6.gif";
		else if (strpos($page_rating, '/ss5.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss5.gif";
		else if (strpos($page_rating, '/ss4.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss4.gif";
		else if (strpos($page_rating, '/ss3.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss3.gif";
		else if (strpos($page_rating, '/ss2.gif'))	$img[$reviews_found] = "http://www.librarything.com/pics/ss2.gif";
		else 										$img[$reviews_found] = "http://www.librarything.com/pics/ss1.gif";
		
		
		// catturiamo il corpo della review
		$page_temp=$page;
		$pos = strpos($page_temp, '<span class=');
		$page_temp=substr($page_temp, 0, $pos);
		$page_temp = str_replace('\t', '', $page_temp);
		$page_temp = str_replace('\n', '', $page_temp);
		$page_temp = str_replace('&', '', $page_temp);	// necessaria perchè il carattere '&' provoca problemi nell'interpretazione del file XML da parte del browser
		$page_temp = substr($page_temp, strpos($page_temp, ">")+1);
		$page_temp = DeleteTagsFromString($page_temp);
		$review[$reviews_found] = $page_temp;
		// catturiamo l'autore della review
		$page_temp=$page;
		$pos = strpos($page_temp, '/profile/');
		$page_temp=substr($page_temp, $pos);
		$page_temp = str_replace('/profile/', '', $page_temp);
		$pos = strpos($page_temp, '"');
		$page_temp=substr($page_temp, 0, $pos);
		$author[$reviews_found]=$page_temp;
		// fine cattura review, passiamo alla successiva
		$reviews_found++;
		$pos = strpos($page, 'div class="bookReview"');
		$page=substr($page, $pos-1); 
	}

	// FASE DI PREPARAZIONE XML OUTPUT
	$_xml  ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
	$_xml .= "<result>\r\n";
	for ($i=0;$i<$reviews_found;$i++) {
		$_xml .="\t<isbn>$isbn</isbn>\r\n";
		$_xml .="\t<tag/>\r\n";
		$_xml .="\t<occurrences/>\r\n";
		$_xml .="\t<user>$author[$i]</user>\r\n";
		$_xml .="\t<review>$review[$i]</review>\r\n";	
		$_xml .="\t<rating>$rate[$i]</rating>\r\n";
		$_xml .="\t<minrating>0.5</minrating>\r\n";
		$_xml .="\t<maxrating>5</maxrating>\r\n";
		$_xml .="\t<imgrating>$img[$i]</imgrating>\r\n";
	}
	$_xml.= "</result>";
	$_xml = str_replace ("&", "&amp;", $_xml);
	// FASE DI SALVATAGGIO FILE XML
	$path='_repositoryXML/';
	$file=$path.$isbn.'_reviews_librarything.xml';
	$fp= fopen($file, "w");
	fwrite($fp, $_xml);
	fclose($fp);
	
	return FixEncoding($_xml);
}
?>