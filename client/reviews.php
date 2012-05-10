<?php //header("Cache-Control:no-cache");?>
<script type="text/javascript" src="lib/tablesorter/jquery.tablesorter.js"></script> 
<script type="text/javascript" src="lib/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
<link rel="stylesheet" href="lib/tablesorter/themes/blue/style.css" type="text/css" >
<link rel="stylesheet" href="lib/tablesorter/addons/pager/jquery.tablesorter.pager.css" type="text/css" >
<script type="text/javascript" src="http://view.jquery.com/tags/ui/latest/ui/ui.dialog.js"></script>
<script type="text/javascript" src="http://view.jquery.com/tags/ui/latest/ui/ui.resizable.js"></script>
<script type="text/javascript" src="http://view.jquery.com/tags/ui/latest/ui/ui.draggable.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/jquery/themes/flora/flora.dialog.css">  
<style type="text/css">

#dinamyc_box_rev {
border:thin solid black;
left:785px;
position:relative;
top:-320px;
width:29%;
}

#dinamyc_tr{
height:40px%;
}

#p_hidden {
visibility:hidden;
font-style: oblique;
}

#pager {

position:static;

}

#close_rev {

width:20px;
height:20px;
cursor:pointer;

}

p {
 font-style: oblique;
-moz-opacity:	1;
opacity:1;
cursor:pointer;	
 }
</style>




<?php
include_once("clean.php"); 
include_once("_params.php"); 


	//--------REVIEW SECTION-----------
	
/* function extact_reviews_rdf($isbn){
	$percorso_rdf = "../Colibrary/rdf/";
	$url=$percorso_rdf.$isbn.'.rdf';
	if(!file_exists($percorso_rdf.$isbn.'_reviews.rdf')){
		@$content1=file_get_contents($url);
		$content2=remove_tag_xml($content1,'coll:biblio');
		$content2=remove_tag_xml($content2,'coll:tags');
		$file_bib=$percorso_rdf.$isbn.'_reviews.rdf';
		$fp=fopen($file_bib,'w');
		fwrite($fp,$content2);
		fclose($fp);
	}
} */
//-------------------------------end of function

$percorso_rdf = "../Colibrary/rdf/";
//extact_reviews_rdf($isbn);

if(@$_SESSION['rev_flag'])	{ ?>
<table style="position:relative;left:538px">
             <tr><td><div ><a href="<?php echo RDF_REPOSITORY . $isbn . '_reviews.rdf'; ?>" target="_blank">
			 <img border="0" src="img/rdf_button.gif" /></a></div></td></tr>
			 <tr><td>&nbsp</td></tr>
</table>

			 <table id="rev_table" class="tablesorter"> 
<thead> 
<tr> 
    <th>User</th>
    <th>Source</th>
    <th>Review</th>
	<th>Rating</th>
</tr> 
</thead> 
<tbody> 
<?php		
	//for($x=0; $x < count($rev); $x++) {
	foreach ($rev as $text => $user) {
		//if the review has an author (only the library thing reviews)
		//Trim the user name from the end of the review (library thing reviews come this way)
		//if(isset($user)) 
		//	$rev[$x] = str_replace($users[$x], "", $rev[$x]);							
		?>
		<tr>
		<?php
		//$id='tr_'.$x;
		if(isset($user)) 
			print '<td><a target="_blank" href="' . $source[$user] . '">' . html_entity_decode($user) . '</a></td>';
		if (strpos($source[$user], 'amazon') != false)
			$site = "Amazon.com";
		elseif (strpos($source[$user], 'anobii') != false)
			$site = "Anobii.com";
		elseif (strpos($source[$user], 'librarything') != false)
			$site = "LibraryThing.com";
		print '<td id="blue_td">' . $site .'</td>';
		//print '<td id='.$id.' class="dinamyc_tr"><p class="p_hidden">'.$rev[$x].'<p></td>'; //print the review
		print '<td class="dinamyc_tr"><p class="p_hidden">' . htmlspecialchars($text) . '<p></td>'; //print the review
		if ($ratings[$text] != "0")
			print '<td id="blue_td"><img src="img/rat_' . str_replace(',', '_', $ratings[$text]) .'.gif" /></td>';
		else
			print '<td id="blue_td"></td>';
		?>
		</tr>
		<?php
	} ?> 
</tbody> 
</table> 
<div id="pager" class="pager">
	<form>
		<img id='first' src="lib/tablesorter/addons/pager/icons/first.png" class="first"/>
		<img id='prev' src="lib/tablesorter/addons/pager/icons/prev.png" class="prev"/>
		<input type="text" class="pagedisplay"/>
		<img id ='next' src="lib/tablesorter/addons/pager/icons/next.png" class="next"/>
		<img id='last' src="lib/tablesorter/addons/pager/icons/last.png" class="last"/>
		<select id='sel_pager' class="pagesize">
			<option selected="selected"  value="5">5</option>
			<option value="10">10</option>
			<option value="15">15</option>
			<option  value="20">20</option>
		</select>
	</form>
</div>

 


<?php } else { print "No Reviews Found!"; }

?>






<script type="text/javascript" src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.core.js"></script>

<script type="text/javascript">

$(document).ready(function() 
    { 
	
	function trunc_tr(){// tronco la review fino a trovare il primo blank dopo il  50^ char 
    rev_list= new Array();
	$(".p_hidden").each(function(){
    rev_list.push($(this).text());
	if ($(this).text().length>50)
	$(this).text($(this).text().substring(0,50)+'...');
     }	);
	}

    function total_rev(){
    $(".dinamyc_tr").click( function(e){ //al click mostro la rev intera
											var obj=$(this);
											$(obj).css({ "background-color" : "#e6EEEE" });
											$("#rev_content").css({
                                                "background-color" : "#F0F0F6",
												"padding" : "10",
												"position" : "absolute",
												"left" : $("#box_rev").offset().left+95,
												"top" : $(this).position().top,
												"width":'540px',
												"zIndex": 2,
												"border": "1px solid black"
											}).show();
											
											$("#rev_content").empty();
											var m=$(this).text();

											$.each(rev_list, function(index,item) {
											
											      if  (item.indexOf(m.substr(0,m.length-3))>-1){
											      $("#rev_content").append("<img id='close_rev'  align= 'right' src='img/close_w.jpg'>");
											      $("#rev_content").append("<div>" +item+ "</div>");
            									  $("#rev_content").show();

									     			$("#close_rev").click( function(e){
						                            $("#rev_content").hide();
													$(obj).css({ "background-color" :"#FFFFFF"  });
													if($(obj).children().size() < 3)
													$(obj).append("<img align= 'right' src='img/icon_ok.gif'>");
	                                                  }	);
												  }
                                            });												 

	});
	
}
	
					//------------------------------------------------end of functions				
	
	trunc_tr();
    $("#content_reviews_req").append('<div id="rev_content" title="Review text"></div>'); // aggiungo il DIV che mostrerà la review per intero
	total_rev();
	
	
	 $(".dinamyc_tr").bind("mouseover", function(){
											$(this).css({ "background-color" : "#F0F0F6" });
											
										})

    $(".dinamyc_tr").bind("mouseleave", function(){
											$(this).css({ "background-color" : "#FFFFFF" });
											
										});
	

	
	$("#rev_table").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#pager")}); 
	$("#rev_content").hide();
    
	
  
  
  

   /* $(".dinamyc_tr").bind("mouseleave", function(){
											$(this).css({ "background-color" : "#FFFFFF" });
											
										});*/

					
});
</script>


  
