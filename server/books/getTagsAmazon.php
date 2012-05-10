<?php
// SEZIONE GET TAGS DAL PORTALE "AMAZON.COM"
function getTagsAmazon($isbn) {
	$url  = "http://www.amazon.com/gp/product/tags-on-product/".$isbn."/ref=tag_dpp_cust_edpp_sa";
	$page = file_get_contents($url);
	$page = strstr($page, "Tags Customers Associate with This Product");
	$tags_found = 0;
	while (strpos($page, "<span tag=")) {
		$page = strstr($page, "<span tag=");
		$temp_pos = strpos($page, "</span><br /></span>");
		$tag[$tags_found] = substr($page, 0, $temp_pos);
		$tags_found++;
		$page = strstr($page, "</span><br /></span>");
	}
	for ($i=0;$i<$tags_found;$i++) {
		// fase di estrazione del nome del tag
		$tag_temp = $tag[$i];
		$pos_start = strpos( $tag_temp, '"');
		$pos_end = strpos( $tag_temp, '><');
		$tag_temp = substr($tag_temp, $pos_start, $pos_end-$pos_start);
		$tag_name[$i] = str_replace('"', '', $tag_temp);
		//fase di estrazione della quantità di occorrenze del tag
		$tag_temp = $tag[$i];
		$pos_start = strpos( $tag_temp, '>(');
		$pos_end = strpos( $tag_temp, ')');
		$tag_temp = substr($tag_temp, $pos_start, $pos_end-$pos_start);
		$tag_occu[$i] = str_replace('>(', '', $tag_temp);

	}
	
	// FASE DI PREPARAZIONE XML OUTPUT
	$_xml  ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
	$_xml .= "<result>\r\n";
	for ($i=0;$i<$tags_found;$i++) {
		$_xml .="\t<isbn>$isbn</isbn>\r\n";
		$_xml .="\t<tag>".$tag_name[$i]."</tag>\r\n";
		$_xml .="\t<occurrences>".$tag_occu[$i]."</occurrences>\r\n";
		$_xml .="\t<user/>\r\n";
		$_xml .="\t<review/>\r\n";
	}
	$_xml.= "</result>";
	
	// FASE DI SALVATAGGIO FILE XML
	$path='_repositoryXML/';
	$file=$path.$isbn.'_tags_amazon.xml';
	$fp= fopen($file, "w");
	fwrite($fp, $_xml);
	fclose($fp);
	
	return $_xml;
}
?>