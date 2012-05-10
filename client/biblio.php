
	<table style="position:relative;left:538px">
		 <tr><td><div ><a href="<?php echo RDF_REPOSITORY . $isbn . '_biblio.rdf'; ?>" target="_blank">
		 <img border="0" src="img/rdf_button.gif" /></a></div></td></tr>
		 <tr><td>&nbsp</td></tr>
	</table>
<?php
	$anobii_link = 'http://www.anobii.com/search?keyword=' . $isbn;
	$librarything_link = 'http://www.librarything.it/search_works.php?q=' . $isbn;        
	print '<h2><font color="#ff9900">' . $title . '</h2></font>';		 		 
	print '<table class="tb_st" cellpadding="10" cellspacing="0" summary="">
			<tr>';
	if (isset($cover)) 
		print '<td valign="top"><img src="' . $cover . '"></td>';
	print '<td>';
	print '<h3>Author(s): ' . $authors;
	print '</h3>
		  <h3>ISBN: ' . $isbn . '</h3>
		  <h3>Editor: ' . $publisher . '</h3>
		  <h3>Publication date: ' . $pub_date . '</h3>';
	if(!empty($pages)) 
		print '<h3>Number of pages: ' . $pages . '</h3>';
	print '	<p>&nbsp;</p>
					<h3>View this book on...</h3>
					<a href="' . $amazon_link . '" target="_blank"><img src="img/amazon.bmp" height="40px"></a>
					<a href="' . $anobii_link . '" target="_blank"><img src="img/anobii.bmp"  height="40px"></a>
					<a href="' . $librarything_link . '" target="_blank"><img src="img/librarything.bmp"  height="40px"></a>
				</td>
				</td>
			  </tr>
	</table>';
?>
