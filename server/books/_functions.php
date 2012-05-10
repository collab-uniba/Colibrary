<?php
include "getTagsAmazon.php";
include "getTagsLibraryThing.php";
include "getTagsAnobii.php";
include "getReviewsAmazon.php";
include "getReviewsLibraryThing.php";
include "getReviewsAnobii.php";

function allowREST($url) {
	// indichiamo qual è il percorso della convenzione REST
	$path = 'mashup/';

	//$pos=strpos($url,'php/');							// $url     = http://................./index.php/0156001314/biblio/
	$pos=strpos($url,$path);							// $url     = http://................./mashup/0156001314/biblio/
	
	$parte = substr($url,$pos+strlen($path));			// $parte = 0156001314/BIBLIO/
	$array = split('/', $parte);
	$tot_param = count($array);
	
	$isbn = $array[0];
	$output = "?isbn=$isbn";
	if ($tot_param>1)
	if ($array[1]=='biblio' || $array[1]=='social')	
		$output.= "&data=$array[1]";
	if ($tot_param>2)
		if ($array[2]=='tags' || $array[2]=='reviews')	
			$output.= "&SocialType=$array[2]";
	if ($tot_param>3)
		if ($array[3]=='amazon' || $array[3]=='anobii'  || $array[3]=='librarything')	{	
			if ($array[2]=='reviews')			$output.= "&ReviewSource=$array[3]";
			else if ($array[2]=='tags')			$output.= "&TagSource=$array[3]";
			else								$output.= "&TagSource=$array[3]&ReviewSource=$array[3]";
		}
	return $output;
}

// risponde se una stringa è in formato UDF-8 oppure no
function mb_detect_encoding2($str) {
    $c=0; $b=0;
    $bits=0;
    $len=strlen($str);
    for($i=0; $i<$len; $i++){
        $c=ord($str[$i]);
        if($c > 128){
            if(($c >= 254)) return false;
            elseif($c >= 252) $bits=6;
            elseif($c >= 248) $bits=5;
            elseif($c >= 240) $bits=4;
            elseif($c >= 224) $bits=3;
            elseif($c >= 192) $bits=2;
            else return false;
            if(($i+$bits) > $len) return false;
            while($bits > 1){
                $i++;
                $b=ord($str[$i]);
                if($b < 128 || $b > 191) return false;
                $bits--;
            }
        }
    }
    return true;
}

// converte una stringa se non è già, in formato UDF-8
function FixEncoding($x){
  if(mb_detect_encoding2($x)=='UTF-8'){
    return $x;
  }else{
    return utf8_encode($x);
  }
}

// VIENE RICHIAMATA NEL FILE index_lunch.php
function FoundAndSetTitle($page) { 
	$pos_start = strpos( $page, '<span id="btAsinTitle" style="">');
	$page = substr($page, $pos_start);
	$page = substr($page, strlen('<span id="btAsinTitle" style="">'));
	$pos_end = strpos( $page, '</span>');
	$page = substr($page, 0, $pos_end);
	$_SESSION['title_for_XML'] = $page;
}   //---------------------------------------------------------------------------------------------

function DeleteTagsFromString($stringa) {

	while (strpos($stringa, "<") < strpos($stringa, ">")) {
		$lenght= strlen($stringa);
		$start = strpos($stringa, "<");
		$end   = strpos($stringa, ">")+1;
		$prima_parte   = substr($stringa, 0, $start);
		$seconda_parte = substr($stringa, $end);
		$stringa = $prima_parte.$seconda_parte;
	}
	$stringa = str_replace('<', '', $stringa);
	$stringa = str_replace('>', '', $stringa);	
	return $stringa;
}


function ISBN_to_ANOBII_ID($isbn)	{
	// controlliamo in primis se abbiamo già trovato in passato l'anobii bookcode di un libro
	$file = "bookcodes_anobii.xml";
	$archivio_bookcodes = file_get_contents($file);
	$pos = strpos($archivio_bookcodes, "<isbn>$isbn</isbn>");
	// SE NON ESISTE NEL FILE XML
	if (!$pos)	{
		// LINK DAL QUALE E' POSSIBILE RECUPERARE I LINK ASSOCIATI AD UN ISBN:	"http://www.anobii.com/search?s=1&keyword=0156001314"
		$url = "http://www.anobii.com/search?s=1&keyword=$isbn";
		$page = file_get_contents($url);
		
		// I LINK CHE CI SERVONO SONO NEL FORMATO 			//	<a href="/books/The_Name_of_the_Rose/9780156001311/0027fdc97402cdda01/">
		$pos = strpos($page, '/books/');
		$temp = substr($page, $pos);				//	/books/The_Name_of_the_Rose/9780156001311/0027fdc97402cdda01/">
		$temp = substr($temp, strlen('/books/'));	// 	The_Name_of_the_Rose/9780156001311/0027fdc97402cdda01/">
		$pos = strpos($temp, '/">');
		$temp = substr($temp, 0, $pos);				// 	The_Name_of_the_Rose/9780156001311/0027fdc97402cdda01
		$pos = strpos($temp, '/');
		
		// estraiamo il codice del titolo
		$codice_titolo = substr($temp, 0, $pos);	// 	The_Name_of_the_Rose 	ESTRATTO
		$temp = substr($temp, $pos+1);				// 	9780156001311/0027fdc97402cdda01
		$pos = strpos($temp, '/');
		
		// abbiamo ottenuto i due codici che ci servivano
		$codice1 = substr($temp, 0, $pos);			// 	9780156001311			ESTRATTO
		$codice2 = substr($temp, $pos+1);			// 	0027fdc97402cdda01		ESTRATTO
		
		// RIUSCIAMO ADESSO A GENERARE I DUE URL CHE SI SERVONO SU Anobii.com PER ESTRARRE TAGS E REVIEWS
		$url_reviews= "http://www.anobii.com/books/$codice_titolo/$codice1/$codice2/"; 
		$url_tags 	= "http://www.anobii.com/books/$codice2/tags/";
		
		// riscriviamo il file anobii_bookcodes.xml con il nuovo libro inserito in archivio
		$pos = strpos($archivio_bookcodes, "</bookcodes_anobii>");
		$archivio_bookcodes  = substr($archivio_bookcodes, 0, $pos);
		$archivio_bookcodes .="\t<book>\r\n";
		$archivio_bookcodes .="\t\t<isbn>".$isbn."</isbn>\r\n";
		$archivio_bookcodes .="\t\t<code>".$codice2."</code>\r\n";
		$archivio_bookcodes .="\t</book>\r\n";
		$archivio_bookcodes .="</bookcodes_anobii>";
		$fp= fopen($file, "w");
		fwrite($fp, $archivio_bookcodes);
		fclose($fp);
		
		return $codice2;
	} else {
	// SE ESISTE NEL FILE XML
		$temp = substr($archivio_bookcodes, $pos);	//	<isbn>102013124</isbn><code>hsiuhwekjdbkdjniuw48736</code>
		$pos  = strpos($temp, '<code>');
		$temp = substr($temp, $pos);				//	<code>hsiuhwekjdbkdjniuw48736</code>
		$pos  = strpos($temp, '>');
		$temp = substr($temp, $pos+1);				//	hsiuhwekjdbkdjniuw48736</code>
		$pos  = strpos($temp, '<');
		$temp = substr($temp, 0, $pos);				//	hsiuhwekjdbkdjniuw48736
		return $temp;
	}
}

function ISBN_to_LT_WORK($isbn)	{
	// controlliamo in primis se abbiamo già trovato in passato l'anobii bookcode di un libro
	$file = "bookcodes_librarything.xml";
	$archivio_bookcodes = file_get_contents($file);
	$pos = strpos($archivio_bookcodes, "<isbn>$isbn</isbn>");
	// SE NON ESISTE NEL FILE XML
	if (!$pos)	{
		// LINK DAL QUALE E' POSSIBILE RECUPERARE IL "WORK" NUMBER A PARTIRE DA UN ISBN:	http://www.librarything.it/search_works.php?q=0156001314
		$url  = "http://www.librarything.com/search_works.php?q=".$isbn;
		$page = file_get_contents($url);
		
		// il "WORK" number è in una stringa così composta: 							<link rel="canonical" href="http://www.librarything.it/work/1525"/>
		$pos = strpos($page, '<link rel="canonical" href=');
		$page = substr($page, $pos);							//	<link rel="canonical" href="http://www.librarything.it/work/1525"/>
		$pos = strpos($page, '/work/');
		$page = substr($page, $pos+strlen('/work/'));			//	1525"/>
		$pos = strpos($page, '"/>');
		$page = substr($page, 0, $pos);							//	1525
		$work=$page;
		
		// riscriviamo il file anobii_bookcodes.xml con il nuovo libro inserito in archivio
		$pos = strpos($archivio_bookcodes, "</bookcodes_librarything>");
		$archivio_bookcodes  = substr($archivio_bookcodes, 0, $pos);
		$archivio_bookcodes .="\t<book>\r\n";
		$archivio_bookcodes .="\t\t<isbn>".$isbn."</isbn>\r\n";
		$archivio_bookcodes .="\t\t<code>".$work."</code>\r\n";
		$archivio_bookcodes .="\t</book>\r\n";
		$archivio_bookcodes .="</bookcodes_librarything>";
		$fp= fopen($file, "w");
		fwrite($fp, $archivio_bookcodes);
		fclose($fp);	
		return $work;
	} else {
	// SE ESISTE NEL FILE XML
		$temp = substr($archivio_bookcodes, $pos);	//	<isbn>102013124</isbn><code>hsiuhwekjdbkdjniuw48736</code>
		$pos  = strpos($temp, '<code>');
		$temp = substr($temp, $pos);				//	<code>hsiuhwekjdbkdjniuw48736</code>
		$pos  = strpos($temp, '>');
		$temp = substr($temp, $pos+1);				//	hsiuhwekjdbkdjniuw48736</code>
		$pos  = strpos($temp, '<');
		$temp = substr($temp, 0, $pos);				//	hsiuhwekjdbkdjniuw48736
		return $temp;
	}
}


// ------------------------------------------------------------------------------------------------


function GET_REVIEW_LIBRARYTHING($isbn)	{
	// LINK DAL QUALE E' POSSIBILE RECUPERARE IL "WORK" NUMBER A PARTIRE DA UN ISBN:	http://www.librarything.it/search_works.php?q=0156001314
	$url  = "http://www.librarything.com/search_works.php?q=".$isbn;
	//$url = "";// DA ELIMINARE
	$page = file_get_contents($url);
	// il "WORK" number è in una stringa così composta: 	<link rel="canonical" href="http://www.librarything.it/work/1525"/>
	// dobbiamo estrarre la sottostringa che indica il "WORK" number
	$pos = strpos($page, '<link rel="canonical" href=');
	$page = substr($page, $pos);
	$pos = strpos($page, '/work/');
	$page = substr($page, $pos+strlen('/work/'));
	$pos = strpos($page, '"/>');
	$page = substr($page, 0, $pos);
	$work=$page;
	// abbiamo ottenuto la corrispondenza tra ISBN e WORK utilizzato da LibraryThing.com
	// LINK DAL QUALE E' POSSIBILILE RECUPERARE LE REVIEW :		http://www.librarything.com/ajaxinc_bookreviews.php?print=1&work= 
	$url  = "http://www.librarything.com/ajaxinc_bookreviews.php?print=1&work=".$work;

	$i = 0;
	$page = file_get_contents($url);
	while (strpos($page, 'div class="bookReview"')) {
		$pos = strpos($page, '<div id=');
		$page=substr($page, $pos); 
		
		// calcoliamo il rating della review
		$pos = strpos($page, 'div class="bookReview"');
		$page_rating=substr($page, 0, $pos); 
		if (strpos($page_rating, '/ss10.gif'))		$rate[$i] = 10;
		else if (strpos($page_rating, '/ss9.gif'))	$rate[$i] = 9;
		else if (strpos($page_rating, '/ss8.gif'))	$rate[$i] = 8;
		else if (strpos($page_rating, '/ss7.gif'))	$rate[$i] = 7;
		else if (strpos($page_rating, '/ss6.gif'))	$rate[$i] = 6;
		else if (strpos($page_rating, '/ss5.gif'))	$rate[$i] = 5;
		else if (strpos($page_rating, '/ss4.gif'))	$rate[$i] = 4;
		else if (strpos($page_rating, '/ss3.gif'))	$rate[$i] = 3;
		else if (strpos($page_rating, '/ss2.gif'))	$rate[$i] = 2;
		else 										$rate[$i] = 1;
		
		// catturiamo il corpo della review
		$page_temp=$page;
		$pos = strpos($page_temp, '<span class=');
		$page_temp=substr($page_temp, 0, $pos);
		$page_temp = str_replace('\t', '', $page_temp);
		$page_temp = str_replace('\n', '', $page_temp);
		$page_temp = str_replace('&', '', $page_temp);	// necessaria perchè il carattere '&' provoca problemi nell'interpretazione del file XML da parte del browser
		$page_temp = substr($page_temp, strpos($page_temp, ">")+1);
		$page_temp = DeleteTagsFromString($page_temp);
		$review[$i] = $page_temp;
		// catturiamo l'autore della review
		$page_temp=$page;
		$pos = strpos($page_temp, '/profile/');
		$page_temp=substr($page_temp, $pos);
		$page_temp = str_replace('/profile/', '', $page_temp);
		$pos = strpos($page_temp, '"');
		$page_temp=substr($page_temp, 0, $pos);
		$author[$i]=$page_temp;
		// fine cattura review, passiamo alla successiva
		$i++;
		$pos = strpos($page, 'div class="bookReview"');
		$page=substr($page, $pos-1); 
	}
	// FASE DI PREPARAZIONE XML OUTPUT
	$reviews_found=$i;
	$_xml = "";
	for ($i=0;$i<$reviews_found;$i++) {
		$_xml .="\t<title>[*title*]</title>\r\n";
		$_xml .="\t<tag/>\r\n";
		$_xml .="\t<occurrences/>\r\n";
		$_xml .="\t<user>$author[$i]</user>\r\n";
		$_xml .="\t<review>$review[$i]</review>\r\n";	
		$_xml .="\t<rating>$rate[$i]</rating>\r\n";
		$_xml .="\t<minrating>1</minrating>\r\n";
		$_xml .="\t<maxrating>10</maxrating>\r\n";
	}
	return $_xml;
}

// ------------------------------------------------------------------------------------------------



function GET_REVIEW_ANOBII($url_reviews)	{
	$page = file_get_contents($url_reviews);
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
	$reviews_found = $i;
	
}

?>