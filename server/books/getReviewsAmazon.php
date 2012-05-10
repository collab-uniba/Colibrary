<?php
// SEZIONE GET REVIEWS DAL PORTALE "AMAZON.COM"
function getReviewsAmazon($isbn, $page_to_parse) {
	$url  = "http://www.amazon.com/review/product/$isbn/ref=cm_cr_pr_link_1?_encoding=UTF8&showViewpoints=0&pageNumber=$page_to_parse";
	$page = file_get_contents($url);

	$page = strstr($page, '<table id="productReviews"');
	$reviews_found = 0;
	while (strpos($page, "people found the following review helpful:")) {
		$page = strstr($page, "people found the following review helpful:");
		$temp_pos = strpos($page, "Was this review helpful to you?");
		$review[$reviews_found] = substr($page, 0, $temp_pos);
		$reviews_found++;
		$page = strstr($page, "Was this review helpful to you?");
	}
	$temp = "";
	for ($i=0;$i<$reviews_found;$i++) {

		// calcoliamo il rating della review
		$page_rating=$review[$i];
		//return $page_rating;
		if (strpos($page_rating, 'stars-5-0'))			$rate[$i] = "5";
		else if (strpos($page_rating, 'stars-4-5'))		$rate[$i] = "4.5";
		else if (strpos($page_rating, 'stars-4-0'))		$rate[$i] = "4";
		else if (strpos($page_rating, 'stars-3-5'))		$rate[$i] = "3.5";
		else if (strpos($page_rating, 'stars-3-0'))		$rate[$i] = "3";
		else if (strpos($page_rating, 'stars-2-5'))		$rate[$i] = "2.5";
		else if (strpos($page_rating, 'stars-2-0'))		$rate[$i] = "2";
		else if (strpos($page_rating, 'stars-1-5'))		$rate[$i] = "1.5";
		else if (strpos($page_rating, 'stars-1-0'))		$rate[$i] = "1";
		else 											$rate[$i] = "0.5";

		// estraiamo i link all'immagine
		if (strpos($page_rating, 'stars-5-0'))			$img[$i] = "http://www.librarything.com/pics/ss10.gif";
		else if (strpos($page_rating, 'stars-4-5'))		$img[$i] = "http://www.librarything.com/pics/ss9.gif";
		else if (strpos($page_rating, 'stars-4-0'))		$img[$i] = "http://www.librarything.com/pics/ss8.gif";
		else if (strpos($page_rating, 'stars-3-5'))		$img[$i] = "http://www.librarything.com/pics/ss7.gif";
		else if (strpos($page_rating, 'stars-3-0'))		$img[$i] = "http://www.librarything.com/pics/ss6.gif";
		else if (strpos($page_rating, 'stars-2-5'))		$img[$i] = "http://www.librarything.com/pics/ss5.gif";
		else if (strpos($page_rating, 'stars-2-0'))		$img[$i] = "http://www.librarything.com/pics/ss4.gif";
		else if (strpos($page_rating, 'stars-1-5'))		$img[$i] = "http://www.librarything.com/pics/ss3.gif";
		else if (strpos($page_rating, 'stars-1-0'))		$img[$i] = "http://www.librarything.com/pics/ss2.gif";
		else 											$img[$i] = "http://www.librarything.com/pics/ss1.gif";		
		
		// fase di estrazione del corpo testo della REVIEW
		$review_temp = $review[$i];
		$pos_end = strpos( $review_temp, '<div style="padding-top: 10px; clear: both; width: 100%;">');
		$review_temp = substr( $review_temp, 0, $pos_end);
		$pos_start = strrpos( $review_temp, '</div>');
		$review_temp = substr($review_temp, $pos_start, $pos_end-$pos_start);
		$review_temp = str_replace('\t', '', $review_temp);
		$review_temp = str_replace('\n', '', $review_temp);
		$review_temp = str_replace('</div>', '', $review_temp);
		// occorre privare di tutti i tag presenti nel corpo della preview, altrimenti provoca problemi nell'interpretazione del file XML
		$review_temp = DeleteTagsFromString($review_temp);
		// necessaria perchè il carattere '&' viene riconoscuto come carattere speciale e provoca problemi nell'interpretazione del file XML da parte del browser (da verificare, succede con l'isdn=0679410449)
		$review_temp = str_replace('&', 'e', $review_temp); 
		$review_text[$i] = $review_temp;
		
		//fase di estrazione dell'autore della REVIEW
		$default = "A Costumer";
		$review_temp = $review[$i];
		$pos_start = strpos( $review_temp, '<span style = "font-weight: bold;">');
		$pos_end = strpos( $review_temp, '</span></a>');
		$review_temp = substr($review_temp, $pos_start, $pos_end-$pos_start);
		$review_temp = str_replace('<span style = "font-weight: bold;">', '', $review_temp);
		$review_auth[$i] = $review_temp;
		if (!$review_auth[$i]) $review_auth[$i] = $default;

	}
	
	// FASE DI PREPARAZIONE XML OUTPUT
	$_xml  ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
	$_xml .= "<result>\r\n";
	for ($i=0;$i<$reviews_found;$i++) {
		$_xml .="\t<isbn>$isbn</isbn>\r\n";
		$_xml .="\t<tag/>\r\n";
		$_xml .="\t<occurrences/>\r\n";
		$_xml .="\t<user>$review_auth[$i]</user>\r\n";
		$_xml .="\t<review>$review_text[$i]</review>\r\n";	
		$_xml .="\t<rating>$rate[$i]</rating>\r\n";
		$_xml .="\t<minrating>0.5</minrating>\r\n";
		$_xml .="\t<maxrating>5</maxrating>\r\n";
		$_xml .="\t<imgrating>$img[$i]</imgrating>\r\n";		
	}
	$_xml.= "</result>";
	$_xml = str_replace ("&", "&amp;", $_xml);
	// FASE DI SALVATAGGIO FILE XML
	$path='_repositoryXML/';
	$file=$path.$isbn.'_reviews_amazon_'.$page_to_parse.'.xml';
	$fp= fopen($file, "w");
	fwrite($fp, $_xml);
	fclose($fp);
	
	return FixEncoding($_xml);
}
?>