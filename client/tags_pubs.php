<style type="text/css">

#tags_info_i {
cursor:pointer;
}
#rdf_tags {
cursor:pointer;
}


</style>
<script type="text/javascript" src="lib/jquery.tooltip.js"></script>
<link rel="stylesheet" href="css/jquery.tooltip.css" type="text/css" >

<script type="text/javascript">

$(document).ready(function() { 
	
			
$('#formInfo').tooltip();	
	
});
</script>

<?php

	function makeTagCloud($tags, $source){
		$useLogCurve = 1;
		if (isset($_GET['linear']))
			$useLogCurve = 0;

		$minFontSize = 10;
		$maxFontSize = 36;
		$fontRange = $maxFontSize - $minFontSize;
		$maxTagCnt = 0;
		$minTagCnt = 10000000;

		foreach ($tags as $tag => $occ) {
			$cnt = $occ;
			if ($occ > $maxTagCnt)
				$maxTagCnt = $occ; 
			if ($occ < $minTagCnt)
				$minTagCnt = $occ; 
		}
		$tagCntRange = $maxTagCnt+1 - $minTagCnt;

		$minLog = log($minTagCnt);
		$maxLog = log($maxTagCnt);
		$logRange = $maxLog - $minLog;
		if ($maxLog == $minLog) 
			$logRange = 1;

		ksort($tags); 
		ksort($source);	

		foreach ($tags as $key => $occ) {
			$href = $source[$key];
			if ($useLogCurve)
			  $fsize = $minFontSize + $fontRange * (log($occ) - $minLog)/$logRange;
			else
			  $fsize = $minFontSize + $fontRange * ($occ - $minTagCnt)/$tagCntRange;
			if (strpos($source[$key], 'bibsonomy') != false) {	
				$color = 'red';
			}
			elseif (strpos($source[$key], 'acm') != false) {
				$color = '#0B479D';			
			}
			elseif (strpos($source[$key], 'citeulike') != false) {
				$color = 'green';
			}			
			print("<a href=\"" . $href . "\" class=\"tags\" target=\"_blank\" style=\"font-size:" . (int)$fsize . "px;color:$color\">" . $key . "</a>\n");
		}
	} 
	//--------------------------------------------------------------------end of function
	
	if(@$_SESSION['tags_flag'])
	{?>
	         <table style="position:relative;left:265px">
			 <tr>
			 <td>
			 <span id="formInfo" title='<div>Clicking on blue tags you will be redirected to tags in Acm</div><br><div>Clicking on red tags you will be redirected to tags in Bibsonomy</div><br><div>Clicking on green tags you will be redirected to tags in Citeulike</div><br>'>
                <img src="img/info.gif" border="0" /></a>
	         </span></td>
			 <td>
			 <div ><a href="<?php echo RDF_REPOSITORY . $interhash . '_tags.rdf'; ?>" target="_blank">
			 <img border="0" src="img/rdf_button.gif"  /></a></div></td>
             
			 </tr>
			 </table>
		<?php 			 
		print '	<table width="100%"> 
		                 <tr><td><div id="tags_info"></div></td></tr>
				         <tr><td><font color="#3333ff">';
						 makeTagCloud($tags, $source);
		print            '</font></td>
						 </tr>
	        	</table>';
	}
	else { print "No Tags Found!"; }
?>