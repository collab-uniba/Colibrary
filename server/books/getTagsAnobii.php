<?php
// SEZIONE GET TAGS DAL PORTALE "AMAZON.COM"
function getTagsAnobii($isbn) {
	$anobii_bookcode = ISBN_to_ANOBII_ID($isbn);
	
	$url = "http://www.anobii.com/books/$anobii_bookcode/tags/";
	$ch = curl_init($url);
	$agent='';
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
	
	$i = 0;
	while (strpos($page, '<a class="rank_')) {
		$page = strstr($page, '<a class="rank_');	//	<a class="rank_4" title="Vedi i libri con l'etichetta 'my favorites'" href="/tags/my%2Bfavorites">my favorites</a>
		$pos = strpos($page, "_");
		$page = substr($page, $pos+1);				//	4" title="Vedi i libri con l'etichetta 'my favorites'" href="/tags/my%2Bfavorites">my favorites</a>
		$pos = strpos($page, '"');
		$OCCORRENZA[$i] = substr($page,0,$pos);		// 	4			ESTRATTO
		$pos = strpos($page, '/tags/');
		$page = substr($page, $pos);				//	/tags/my%2Bfavorites">my favorites</a>
		$pos = strpos($page, '>');
		$page = substr($page, $pos+1);				//	my favorites</a>
		$pos = strpos($page, '<');
		$TAG[$i] = substr($page, 0, $pos);			// 	my favorites	ESTRATTO
		$i++;
	}
	$tags_found = $i;
	
	// FASE DI PREPARAZIONE XML OUTPUT
	$_xml  ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
	$_xml .= "<result>\r\n";
	for ($i=0;$i<$tags_found;$i++) {
		$_xml .="\t<isbn>$isbn</isbn>\r\n";
		$_xml .="\t<tag>".$TAG[$i]."</tag>\r\n";
		$_xml .="\t<occurrences>".$OCCORRENZA[$i]."</occurrences>\r\n";
		$_xml .="\t<user/>\r\n";
		$_xml .="\t<review/>\r\n";
	}
	$_xml.= "</result>";
	
	// FASE DI SALVATAGGIO FILE XML
	$path='_repositoryXML/';
	$file=$path.$isbn.'_tags_anobii.xml';
	$fp= fopen($file, "w");
	fwrite($fp, $_xml);
	fclose($fp);
	
	return $_xml;
}
?>