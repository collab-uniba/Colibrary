<?php

function getCiteulikeResourcePage($title){
$url="http://www.citeulike.org/search/all?q=".$title;
$Citeulike_research=@fopen($url,'rb');
$contents = "";
 if (!$Citeulike_research){
	 generateError(8);
     return FALSE;
    }
  else {
     while (!feof($Citeulike_research)) {
     $contents .=fread($Citeulike_research, 4096);
	    } 
       fclose($Citeulike_research);
    }
	
   if(strpos($contents,'<a class="title"')!=FALSE){
    
    $pos=strpos($contents,'<a class="title"');
    $parte=strstr($contents,'<a class="title"');
    $pos=strpos($parte,'<a class="title"');
    $pos2=strpos($parte,'">');
	$sub=substr($parte, $pos, $pos2);
	$sub=strstr($sub,'/');
	$resource_page="http://www.citeulike.org".$sub;
	return $resource_page;
   
      }
   
   else{	
	  
	  return FALSE;
  	}
}//fine funzione getCiteulikeResourcePage

function getCiteulikeBibtex($url){
$Citeulike_bib="Citeulike_bib.xml";
$fp=fopen($Citeulike_bib,"w");
$Citeulike_resource=@fopen($url,'rb');
$contents = "";
 if (!$Citeulike_resource){
     return FALSE;
    }
  else {
     while (!feof($Citeulike_resource)) {
     $contents .=fread($Citeulike_resource, 4096);
	    } 
       fclose($Citeulike_resource);
    }
  $pos=strpos($contents,'bibtex-body');
  $parte=strstr($contents,'bibtex-body');
  $pos=strpos($parte,'bibtex-body');
  $pos2=strpos($parte,'</textarea>');
  $parte=substr($parte, $pos, $pos2);
  $pos=strpos($parte,',');
  $pos2=strrpos($parte,'}');
  $sub=substr($parte, $pos+1, $pos2);
  $pos2=strrpos($sub,'}');
  $sub=substr($sub,0,$pos2);
  
  $p=strpos($sub,'url');
  $pa=strstr($sub,'url');
  $p=strpos($pa,'url');
  $p2=strpos($pa,'}');
  $pa=substr($pa,$p,$p2+1);
  $sub=str_replace($pa,'',$sub);
  
  //$sub=str_replace('=','',$sub);
  //$sub=str_replace('{','= {',$sub);
  $sub=str_replace('&#34;','',$sub);
   $sub=str_replace('"','',$sub);
  $sub=str_replace('},','"',$sub);
  $sub=str_replace('{','',$sub);
  $sub=str_replace('}','',$sub);
  $sub=str_replace('{{','',$sub);
  $sub=str_replace('}}','',$sub);
  $sub=str_replace("\'\\","\'",$sub);
  $sub=str_replace(',,','',$sub);
  $sub=str_replace(',','',$sub);
  $sub=str_replace ('=','= "',$sub);
  $sub=$sub.'"';
  //$sub=str_replace ('\'','&#39;',$sub); 
  $latex_conversion_table = array("\&#39;a" => "á", "\`a" => "à", "\&#94;a" => "â", "\&#126;a" => "ã", "\\\&quot;a" => "ä", "\aa" => "å", "\ae" => "æ",
              "\cc" => "ç", "\cC" => "Ç",
              "\&#39;e" => "é", "\&#94;e" => "ê", "\`e" => "è", "\\\&quot;e" => "ë",
              "\&#39;i" => "í", "\`i" => "ì", "\&#94;i" => "î", "\\\&quot;i" => "ï",
              "\&#126;n" => "ñ",
              "\&#39;o" => "ó", "\&#94;o" => "ô", "\`o" => "ò", "\\\&quot;o" => "ö", "\&#126;o" => "õ", "\\\&quot;O" => "Ö",
              "\&#39;u" => "ú", "\`u" => "ù", "\&#94;u" => "û", "\\\&quot;u" => "ü", "\\\&quot;U" => "Ü", 
              "\&#39;y" => "ý", "\\\&quot;y" => "ÿ");

	$sub=str_replace(array_keys($latex_conversion_table),
                       array_values($latex_conversion_table),
                       $sub);
  

  
  $sub="<bibtex ".$sub."/>";
  $sub="<bib>\n".$sub."</bib>";
  $sub="<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\" ?>\n".$sub;
  fwrite($fp,$sub);
  fclose($fp);
  return $Citeulike_bib;
  
}



function getCiteulikeUsers($url){
$Citeulike_usersXML="Citeulike_users.xml";
$fp=fopen($Citeulike_usersXML,"w");
$Citeulike_user=@fopen($url,'rb');
$contents = "";
 if (!$Citeulike_user){
     return FALSE;
    }
  else {
     while (!feof($Citeulike_user)) {
     $contents .=fread($Citeulike_user, 4096);
	    } 
       fclose($Citeulike_user);
    }
	
  $pos=strpos($contents,'posters-body');
  $parte=strstr($contents,'posters-body');
  $pos=strpos($parte,'posters-body');
  $pos2=strpos($parte,'</span>');
  $parte=substr($parte, $pos, $pos2);
  $parte=str_replace('posters-body"><ul><li class="menu"><span class="black">','',$parte);
  $parte=str_replace(',','',$parte);
  $parte='<users>'.$parte.'</users>';
  $parte="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>\n".$parte;
  fwrite($fp,$parte);
  fclose($fp);
  return $Citeulike_usersXML;
}//fine funzione



function getCiteulikeTags($url){
$Citeulike_tags="Citeulike_tags.xml";
$fp=fopen($Citeulike_tags,"w");
$Citeulike_resource=@fopen($url,'rb');
$contents = "";
 if (!$Citeulike_resource){
     return FALSE;
    }
  else {
     while (!feof($Citeulike_resource)) {
     $contents .=fread($Citeulike_resource, 4096);
	    } 
       fclose($Citeulike_resource);
    }
  $pos=strpos($contents,'<a class="tag" rel');
  $parte=strstr($contents,'<a class="tag" rel');
  $pos=strpos($parte,'<a class="tag" rel');
  $pos2=strpos($parte,'</span>');
  $parte=substr($parte, $pos, $pos2);
  $parte=str_replace(',','',$parte);
  $parte="<tags>\n".$parte."\n</tags>";
  $parte="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$parte;
  fwrite($fp,$parte);
  fclose($fp);
  return $Citeulike_tags;

}//fine funzione


function getCiteulikeReviews($url){
$Citeulike_revXML="Citeulike_rev.xml";
$fp=fopen($Citeulike_revXML,"w");
$Citeulike_rev=@fopen($url,'rb');
$contents = "";
 if (!$Citeulike_rev){
     return FALSE;
    }
  else {
     while (!feof($Citeulike_rev)) {
     $contents .=fread($Citeulike_rev, 4096);
	    } 
       fclose($Citeulike_rev);
    }
  $pos=strpos($contents,'<div id="reviews-body">');
  $parte=strstr($contents,'<div id="reviews-body">');
  $pos=strpos($parte,'<div id="reviews-body">');
  $pos2=strpos($parte,'<h3>');
  $parte=substr($parte, $pos, $pos2);
  fwrite($fp,$parte);
  fclose($fp);
  return $Citeulike_revXML;
}//fine funzione

function getCiteulikeReviewsArray($xml){

$root=simplexml_load_file($xml);

$user=array();
$text=array();
$date=array();
foreach ($root->blockquote as $rev){
array_push($text, $rev->p);
}

foreach ($root->div as $us){
array_push($user,$us->a);
}

foreach ($root->div as $da){
$da=str_replace('Reviewed by','',$da);
array_push($date,$da);
}


$review=array("text"=>$text,"date"=>$date,"user"=>$user);


return $review;
}//fine funzione

//prende in input i 2 file xml (bibtex e tags) creati nelle funzioni precendenti e crea Info_Citeulike.xml
function getCiteulikeXML($bibtex,$tags,$users,$rev,$url_resource){

  $info_Citeulike="info_Citeulike.xml";
  $fp=fopen($info_Citeulike,"w");
  $header="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>\n";
  $root="<post source=\"Citeulike\">";
  $close_root="</post>";
  $root_bibtex=simplexml_load_file($bibtex);
  $title=$root_bibtex->bibtex['title'];
  $author=$root_bibtex->bibtex['author'];
  $abstract=$root_bibtex->bibtex['abstract'];
  $doi=$root_bibtex->bibtex['doi'];
  $issn=$root_bibtex->bibtex['issn'];
  $isbn=$root_bibtex->bibtex['isbn'];
  $url=$root_bibtex->bibtex['url'];
  $journal=$root_bibtex->bibtex['journal'];
  $publisher=$root_bibtex->bibtex['publisher'];
  $pages=$root_bibtex->bibtex['pages'];
  $year=$root_bibtex->bibtex['year'];
  $editor=$root_bibtex->bibtex['editor'];
  $tag_href_vect=grab_tags_Citeulike_href($tags);
  $tag_label_vect=grab_tags_Citeulike_label($tags);
  $user_href_vect=grab_users_Citeulike_href($users);
  $user_label_vect=grab_users_Citeulike_label($users);
  $url=str_replace('&','&amp;',$url); 
  //$url=str_replace('"','',$url);
  $url_resource=str_replace('&','&amp;',$url_resource); 
  $abstract=str_replace('"','&#39;',$abstract);
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
  $content.="\n"."<citeulike_url type=\"biblio\" href=\"".$url_resource."\" />";
  for ($i=0;$i<count($tag_label_vect);$i++){
  $content.="\n"."<tag type=\"social\" href=\"".$tag_href_vect[$i]."\">".$tag_label_vect[$i]."</tag>";
  }
  
  for ($i=0;$i<count($user_label_vect);$i++){
  $content.="\n"."<user type=\"social\" href=\"http://www.citeulike.org".$user_href_vect[$i]."\">".$user_label_vect[$i]."</user>";
  }
  
  for ($i=0;$i<count($rev["text"]);$i++) {
  $content.="\n"."<review type=\"social\" date=\"".$rev["date"][$i]."\" author=\"".$rev["user"][$i]."\">".$rev["text"][$i]."</review>";
  
  }
  
  $total=$header."\n".$root."\n".$content."\n".$close_root;
  $total=str_replace('\'','&quot;',$total);
  fwrite($fp,$total);
  fclose($fp);
  return $info_Citeulike;
}//fine funzione

function grab_tags_Citeulike_href($tags){
  $root=simplexml_load_file($tags);
  $array_tag_href=array();
  foreach ($root->a as $a)
  array_push($array_tag_href,'http://www.citeulike.org'.$a['href']);
  return $array_tag_href;

}


function grab_tags_Citeulike_label($tags){
  $root=simplexml_load_file($tags);
  $array_tag_label=array();
  foreach ($root->a as $a)
  array_push($array_tag_label,$a);
  return $array_tag_label;

}

function grab_users_Citeulike_label($users){
  $root=simplexml_load_file($users);
  $array_user_label=array();
  foreach ($root->a as $label)
  array_push($array_user_label,$label);
  return $array_user_label;

}

function grab_users_Citeulike_href($users){
  $root=simplexml_load_file($users);
  $array_user_href=array();
  foreach ($root->a as $href)
  array_push($array_user_href,$href['href']);
  return $array_user_href;

}
?>