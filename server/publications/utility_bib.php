<?php

//accede e copia le api di bibsonomy sul server locale					
function download($url,$local_file){     
	$user='antogrim2';
	$pass='260aa1fa60ef2bebec3c7092bbc15c3e';     
	$ch = curl_init($url);
	//curl_setopt($ch, CURLOPT_GET,1);
	curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
	curl_setopt($ch, CURLOPT_HEADER,0);
	curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$fp = fopen($local_file, "w");
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	fclose($fp);
}//fine funtcion download											

function getBibsonomyXMLApi($title){
	$url = 'http://bibsonomy.org/api/posts?search='.$title.'&resourcetype=bibtex';
	$local='bibsonomy_api.xml';
	$local_tag='bibsonomy_tag.xml';
	download($url,$local);
	if (file_exists($local)) {
		//estrae l'interhash
		$url_interhash=getPostInterhash($local);
		// estrae un file xml contenente i tag dalla pagina dell pubblicazione dato l'interhash
		$fp = fopen($local_tag, "w");
		$xml_tag="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>\n"."<tags>".getPostTags($url_interhash)."</tags>";
		fwrite($fp,$xml_tag);
		fclose($fp);
		$tag_href_vect=grab_tags_bibsonomy_href($local_tag);
		$tag_occurr_vect=grab_tags_bibsonomy_occurr($local_tag);
		$tag_label_vect=grab_tags_bibsonomy_label($local_tag);
		$username_vect=grab_users_bibsonomy($local);
		//crea in file xml con info sociali e bibliografiche
		$bib_info=getBibsonomyInfo($local,$tag_href_vect,$tag_occurr_vect,$tag_label_vect,$username_vect);
		return $bib_info;
	} 
	else{	
		generateError(1);
		return FALSE;
	}
}

function grab_users_bibsonomy($xml){
	$username=array();
	$root=simplexml_load_file($xml);
	foreach ($root->posts->post as $post)
		array_push($username,$post->user['name']);
	$username=array_unique($username);
	return $username;
}

//recupera gli href dei tag dal file local_tag e li mette in un array
function grab_tags_bibsonomy_href($xml){
	$root=simplexml_load_file($xml);
	$array_tag_href=array();
	foreach ($root->ul->li as $li)
		array_push($array_tag_href,'http://www.bibsonomy.org'.$li->a['href']);
	return $array_tag_href;
}

//recupera le occorrenze dei tag dal file local_tag e li mette in un array
function grab_tags_bibsonomy_occurr($xml){
	$root=simplexml_load_file($xml);
	$array_tag_occurr=array();
	foreach ($root->ul->li as $li)
		array_push($array_tag_occurr,$li->a['title']);
	return $array_tag_occurr;
}

//recupera le label dei tag dal file local_tag e li mette in un array
function grab_tags_bibsonomy_label($xml){
	$root=simplexml_load_file($xml);
	$array_tag_label=array();
	foreach ($root->ul->li as $li)
		array_push($array_tag_label,$li->a);
	return $array_tag_label;
}

function getPostInterhash($xml){
	$root=simplexml_load_file($xml);
	$interhash=$root->posts->post[0]->bibtex['interhash'];
	$url_interhash='http://www.bibsonomy.org/bibtex/'.$interhash;
	return $url_interhash;
}

//dalla pagina della risorsa ricaviamo un file xml contenente tutti i tag della risorsa con le info (occorrenza e url) associate
function getPostTags($url_interhash){

	$bibsonomy_bibtex=@fopen($url_interhash,'rb');
	$contents = "";
	if(!$bibsonomy_bibtex){
		return FALSE;
	}
	else {
	while (!feof($bibsonomy_bibtex)) {
		$contents .=fread($bibsonomy_bibtex, 4096); 
	} 
	fclose($bibsonomy_bibtex);
	}
	if (strpos($contents,'<ul id="tagbox"')!=FALSE){
		$pos=strpos($contents,'<ul id="tagbox"');
		$parte=strstr($contents,'<ul id="tagbox"');
		$pos=strpos($parte,'<ul id="tagbox"');
		$pos2=strpos($parte,'</ul>');
		return substr($parte, $pos, $pos2+5);
	}
	else{	
		return FALSE;
	}
}//fine funzione

function getBibsonomyInfo($xml,$href,$occurr,$label,$user){
	$local='bibsonomy_api.xml';
	$url_interhash=getPostInterhash($local);
	$info_bibsonomy="info_bibsonomy.xml";
	$fp=fopen($info_bibsonomy,"w");
	$header="<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>\n";
	$root="<post source=\"bibsonomy\">";
	$close_root="</post>";
	$bibsonomy_api_root=simplexml_load_file($xml);
	$title=$bibsonomy_api_root->posts->post[0]->bibtex['title'];
	$author=$bibsonomy_api_root->posts->post[0]->bibtex['author'];
	$publisher=$bibsonomy_api_root->posts->post[0]->bibtex['publisher'];
	$journal=$bibsonomy_api_root->posts->post[0]->bibtex['booktitle'];
	//$journal=$bibsonomy_api_root->posts->post[0]->bibtex['journal'];
	$year=$bibsonomy_api_root->posts->post[0]->bibtex['year'];
	$editor=$bibsonomy_api_root->posts->post[0]->bibtex['editor'];
	$url=$bibsonomy_api_root->posts->post[0]->bibtex['url'];
	$abstract=$bibsonomy_api_root->posts->post[0]->bibtex['bibtexAbstract'];
	$pages=$bibsonomy_api_root->posts->post[0]->bibtex['pages'];
	$misc=$bibsonomy_api_root->posts->post[0]->bibtex['misc'];
	$misc=str_replace(" = ","=",$misc);

	// estrai isbn da misc
	$pos=strpos($misc,'isbn={'); 
	$parte=strstr($misc,'isbn={');
	$pos=strpos($parte,'isbn={');
	$pos2=strpos($parte,'}');
	$isbn=substr($parte,$pos,$pos2);
	$isbn=str_replace('isbn={','',$isbn);

	// estrai doi da misc
	$pos=strpos($misc,'doi={'); 
	$parte=strstr($misc,'doi={');
	$pos=strpos($parte,'doi={');
	$pos2=strpos($parte,'}');
	$doi=substr($parte,$pos,$pos2);
	$doi=str_replace('doi={','',$doi);

	// estrai doi da misc
	$pos=strpos($misc,'ee={'); 
	$parte=strstr($misc,'ee={');
	$pos=strpos($parte,'ee={');
	$pos2=strpos($parte,'}');
	$ee=substr($parte,$pos,$pos2);
	$ee=str_replace('ee={','',$ee);
	if (strpos($ee,'doi')!=FALSE) $doi=$ee;

	//estrai issn da misc
	$pos=strpos($misc,'issn={'); 
	$parte=strstr($misc,'issn={');
	$pos=strpos($parte,'issn={');
	$pos2=strpos($parte,'}');
	$issn=substr($parte,$pos,$pos2);
	$issn=str_replace('issn={','',$issn);

	// pulisce l'url dalla e commerciale
	//$url=str_replace('&','&amp;',$url);

	// pulisce l'abstract dalle " 
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
	$content.="\n"."<bibsonomy_url type=\"biblio\" href=\"".$url_interhash."\" />";
	$content.="\n"."<doi type=\"biblio\">".$doi."</doi>";
	$content.="\n"."<isbn type=\"biblio\">".$isbn."</isbn>";
	$content.="\n"."<issn type=\"biblio\">".$issn."</issn>";
	for ($i=0;$i<count($label);$i++){
		$content.="\n"."<tag type=\"social\" href=\"".$href[$i]."\" occurrence=\"".$occurr[$i]."\">".$label[$i]."</tag>";
	}
	for ($i=0;$i<count($user);$i++){
		$content.="\n"."<user type=\"social\" href=\"http://bibsonomy.org/user/".$user[$i]."\">".$user[$i]."</user>";
	}

	$total=$header."\n".$root."\n".$content."\n".$close_root;
	$total=str_replace("&","&amp;",$total);
	fwrite($fp,$total);
	fclose($fp);
	return $info_bibsonomy;
}// fine funzione

?>