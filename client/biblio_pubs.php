
	<table style="position:relative;left:538px">
		 <tr><td><div ><a href="<?php echo RDF_REPOSITORY . $interhash . '_biblio.rdf'; ?>" target="_blank">
		 <img border="0" src="img/rdf_button.gif" /></a></div></td></tr>
		 <tr><td>&nbsp</td></tr>
	</table>
<?php
	if ($bibsonomy_link == '')
		$bibsonomy_link = 'http://www.bibsonomy.org/search/' . $title;
	if ($acm_link == '')
		if (strpos($doi, 'acm.org') != false) {
			$temp = split('/', $doi);
			$acm_link = 'http://portal.acm.org/citation.cfm?doid=' . $temp[count($temp)-1];
		} else
			$acm_link = 'http://portal.acm.org/results.cfm?coll=portal&dl=ACM&CFID=35865460&CFTOKEN=77765839&query=PublicationTitle %22' . $title . '%22&termshow=matchboolean';        
	if ($citeulike_link == '')
		$citeulike_link = 'http://www.citeulike.org/search/all?q=' . $title; 
	print '<h2><font color="#ff9900">' . $title . '</h2></font>';		 		 
	//print '<table class="tb_st" cellpadding="10" cellspacing="0" summary="">';
	print '<h3>Author(s): ' . $authors . '</h3>';
	print '<h3>Interhash: ' . $interhash . '</h3>';
	if ($description != '')
		print '<h3>Abstract: <div style="font-family: Verdana, Arial, Helvetica, sans-serif;text-align:justify; font-weight:300; font-size:12px; width:98%">' . $description . '</div></h3>';
	if ($journal != '') {
		$value = '<h3>Journal: ' . $journal; 
		if ($pages != '') 
			$value .= ' (Pages: ' . $pages . ')';
		$value .= '</h3>';
		print $value;
	}
	if ($date != '')
		print '<h3>Year of Publication: ' . $date . '</h3>';
	if ($publisher != '')
		print '<h3>Publisher: ' . $publisher . '</h3>';
	if ($editor != '')
		print '<h3>Contributor: ' . $editor . '</h3>';
	if ($doi != '') {
		if (substr(trim($doi), 0, 4) == 'http') {
			$l = $doi;
			$v = str_replace(array('http://dx.doi.org/', 'http://doi.acm.org/'), '', $doi);
		} else { 
			$v = trim(str_replace('\\', '', $doi));
			$l = 'http://dx.doi.org/' . $v;			
		}
		print '<h3>DOI: <a class="linkbiblio" href="' . $l . '" target="_blank">' . $v . '</a></h3>';	
	}
	if ($isbn != '')
		print '<h3>ISBN: <a class="linkbiblio" href="' . $isbn . '" target="_blank">'. str_replace('http://www4.wiwiss.fu-berlin.de/bookmashup/books/', '', $isbn) .'</a></h3>';
	if ($issn != '')
		print '<h3>ISSN: ' . $issn . '</h3>';	
	print '	<p>&nbsp;</p>
					<h3>View this publication on...</h3>
					<a href="' . $bibsonomy_link . '" target="_blank"><img src="img/bibsonomy.bmp" height="40px" width ="140px"></a>
					<a href="' . $acm_link . '" target="_blank"><img src="img/acm.bmp"  height="40px"></a>
					<a href="' . $citeulike_link . '" target="_blank"><img src="img/citeulike.bmp"  height="40px"></a>
				</td>
				</td>
			  </tr>';
	//</table>';
?>

	
