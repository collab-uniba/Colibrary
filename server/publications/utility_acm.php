<?php

function verificaACM($translated_title){

$translated_title=str_replace(' ','+',$translated_title);
$url='http://www.google.it/search?hl=it&q='.$translated_title.'+portal+acm&meta=&aq=f&oq=';
$fp=fopen($url,'rb');
$contents = "";
  
     while (!feof($fp)) {
     $contents .=fread($fp, 128);
	    } 
       fclose($fp);
    
    $pos=strpos($contents,'<h3 class=r>');
    $parte=strstr($contents,'<h3 class=r>');
    $pos=strpos($parte,'<h3 class=r>');
    $pos2=strpos($parte,'</h3>');
	$sub=substr($parte, $pos, $pos2);
	$sub=$sub."</h3>";
	$sub=str_replace('</em>','',$sub);
	$sub=str_replace('<em>','',$sub);
	$sub=str_replace('class=l','',$sub);
	$sub=str_replace('class=r','',$sub);

	$sub="<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\" ?>\n".$sub;
	$root=simplexml_load_string($sub);
	
	foreach ($root->a as $a)
	    if (stripos($a['href'],'acm')) return $a['href'];
		else {
			 generateError(10);

		return "error";
		}

}		



function downloadacm($url){
$acm_page="acm_page.html";
$fp1=fopen($acm_page,'w');
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER,0);
curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_FILE, $fp1);
curl_exec($ch);
fclose($fp1);
return $acm_page;

}


function getAcmBibtexUrl($url){
$Acm_bibtex_research=@fopen($url,'rb');
$contents = "";
 if (!$Acm_bibtex_research){
     return FALSE;
    }
  else {
     while (!feof($Acm_bibtex_research)) {
     $contents .=fread($Acm_bibtex_research, 4096);
	    } 
       fclose($Acm_bibtex_research);
    }
    $pos=strpos($contents,'popBibTex');
    $parte=strstr($contents,'popBibTex');
    $pos=strpos($parte,'popBibTex');
    $pos2=strpos($parte,"'");
	$sub=substr($parte, $pos, $pos2);
	
	$sub="http://portal.acm.org/".$sub;
	//echo $sub;
	return $sub;
	
}//fine funzione



// restituisce il file di testo del bibtex
function getAcmBibtex($url){

$Acm_bibtex="Acm_bibt.txt";
$fp1=fopen($Acm_bibtex,"w");
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER,0);
curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_FILE, $fp1);
curl_exec($ch);
fclose($fp1);

return $Acm_bibtex;
} // fine

function getAcmBibtexXML($local){
$Acm_bibtexXML="Acm_bib.xml";
$fp=fopen($Acm_bibtexXML,"w");

$local=@fopen($local,'rb');
$contents = "";
 if (!$local){
     return FALSE;
    }
  else {
     while (!feof($local)) {
     $contents .=fread($local, 4096);
	    } 
       fclose($local);
    }
	
	$contents=str_replace(',,','',$contents);
   $pos=strpos($contents,'@');
    $parte=strstr($contents,'@');
    $pos=strpos($parte,'@');
    $pos2=strpos($parte,'</pre>');
	$sub=substr($parte, $pos, $pos2);
	$pos=strpos($sub,',');
    $pos2=strrpos($sub,'}');
    $sub=substr($sub, $pos+1, $pos2);
	$sub=str_replace('},','"',$sub);
	$sub=str_replace('{','',$sub);
	$sub=str_replace('}','',$sub);
	$sub=str_replace("\'\\","\'",$sub);
   	$sub=str_replace ('=','= "',$sub);

$latex_conversion_table = array("\'a" => "á", "\`a" => "à", "\^a" => "â", "\~a" => "ã", "\\\"a" => "ä", "\aa" => "å", "\ae" => "æ",
              "\cc" => "ç", "\cC" => "Ç",
              "\'e" => "é", "\^e" => "ê", "\`e" => "è", "\\\"e" => "ë",
              "\'i" => "í", "\`i" => "ì", "\^i" => "î", "\\\"i" => "ï",
              "\~n" => "ñ",
              "\'o" => "ó", "\^o" => "ô", "\`o" => "ò", "\\\"o" => "ö", "\~o" => "õ", "\\\"O" => "Ö",
              "\'u" => "ú", "\`u" => "ù", "\^u" => "û", "\\\"u" => "ü", "\\\"U" => "Ü", 
              "\'y" => "ý", "\\\"y" => "ÿ");

	$sub=str_replace(array_keys($latex_conversion_table),
                       array_values($latex_conversion_table),
                       $sub);

	
	
  
  
    $sub.="\n";
	$sub="<bibtex ".$sub."/>";
    $sub="<bib>\n".$sub."</bib>";
    $sub="<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\" ?>\n".$sub;
	fwrite($fp,$sub);
    fclose($fp);
	
	

	
    return $Acm_bibtexXML;
}//fine funzione

function getAcmAbstract($url){
$Acm_abstract=@fopen($url,'rb');
$contents = "";
 if (!$Acm_abstract){
     return FALSE;
    }
  else {
     while (!feof($Acm_abstract)) {
     $contents .=fread($Acm_abstract, 4096);
	    } 
       fclose($Acm_abstract);
    }
	$pos=strpos($contents,'<p class="abstract">');
    $parte=strstr($contents,'<p class="abstract">');
    $pos=strpos($parte,'<p class="abstract">');
    $pos2=strpos($parte,'</p>');
	$abstract=substr($parte, $pos, $pos2);
	$abstract=str_replace('"','',$abstract);
	//echo $abstract;
	//$abstract=htmlspecialchars($abstract);
	return $abstract;

}//fine funzione

function getAcmKeywords($url){
$Acm_tagXML="Acm_tag.xml";
$fp=fopen($Acm_tagXML,"w");

$key_acm=@fopen($url,'rb');
//sleep(10);
$contents = "";
 if (!$key_acm){
     return FALSE;
    }
  else {
     while (!feof($key_acm)) {
     $contents .=fread($key_acm, 4096);
	    } 
       fclose($key_acm);
    }
	//echo $contents;
	$pos=strpos($contents,'<p class="keywords">');
    $parte=strstr($contents,'<p class="keywords">');
    $pos=strpos($parte,'<p class="keywords">');
    $pos2=strpos($parte,'</p>');
	$sub=substr($parte, $pos, $pos2);
	$sub=str_replace('<p class="keywords">','',$sub);
	$sub=str_replace('<SPAN class=heading><A NAME="Keywords">Keywords:</A></span>','',$sub);
	$sub=str_replace('<BR>','',$sub);
	$sub=str_replace('target="_self"','',$sub);
	$sub=str_replace('&','&amp;',$sub);	
    $sub=str_replace(',','',$sub);
	
	$sub="<keywords>\n".$sub."</keywords>";
	$sub=str_replace('</kwd>','',$sub);
	$keywordsXML="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$sub;
	
    fwrite($fp,$keywordsXML);
    fclose($fp);
	return $Acm_tagXML;
	
}//fine funzione

function grab_tags_acm($Acm_tagXML){
    $root=simplexml_load_file($Acm_tagXML);
	$array_keywords=array();
    foreach ($root->a as $a){
	
	$a=str_replace('.','',$a);
	
    array_push($array_keywords,$a);
	}
	
    return $array_keywords;
}

function getAcmXML($Acm_bibtexXML,$url_acm,$tag,$abstract){
$info_Acm="info_Acm.xml";
$fp=fopen($info_Acm,"w");
$header="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>\n";
$root_xml="<post source=\"Acm\">";
$close_root="</post>";
$root=simplexml_load_file($Acm_bibtexXML);
$title=$root->bibtex['title'];
$author=$root->bibtex['author'];
//$journal=$root->bibtex['journal'];
$journal=$root->bibtex['booktitle'];
$publisher=$root->bibtex['publisher'];
$doi=$root->bibtex['doi'];
$isbn=$root->bibtex['isbn'];
$issn=$root->bibtex['issn'];
$year=$root->bibtex['year'];
$pages=$root->bibtex['pages'];
$editor=$root->bibtex['editor'];
$url_acm=str_replace('&','&amp;',$url_acm); 
$url=str_replace('&','&amp;',$url);

$abstract=str_replace('<p class=abstract>','',$abstract);
$abstract=str_replace('<p>','',$abstract);
$abstract=str_replace('"','&#39;',$abstract);
$abstract=str_replace('&rsquo;','',$abstract);
$abstract=str_replace('&lsquo;','',$abstract); 

  $content="<title type=\"biblio\">".$title."</title>";
  $content.="\n"."<authors type=\"biblio\">".$author."</authors>";
  $content.="\n"."<abst type=\"biblio\">".$abstract."</abst>";
  $content.="\n"."<editors type=\"biblio\">".$editor."</editors>";
  $content.="\n"."<publisher type=\"biblio\">".$publisher."</publisher>";
  $content.="\n"."<journal type=\"biblio\">".$journal."</journal>";
  $content.="\n"."<pages type=\"biblio\">".$pages."</pages>";
  $content.="\n"."<year type=\"biblio\">".$year."</year>";
  $content.="\n"."<url type=\"biblio\" url=\"".$url."\" />";
  $content.="\n"."<doi type=\"biblio\">".$doi."</doi>";
  $content.="\n"."<isbn type=\"biblio\">".$isbn."</isbn>";
  $content.="\n"."<issn type=\"biblio\">".$issn."</issn>";
  $content.="\n"."<acm_url type=\"biblio\" href=\"".$url_acm."\" />";
  for ($i=0;$i<count($tag);$i++){
  $content.="\n"."<tag type=\"social\">".$tag[$i]."</tag>";
  }
  $total=$header."\n".$root_xml."\n".$content."\n".$close_root;

  fwrite($fp,$total);
  fclose($fp);
  return $info_Acm;
}



?>
