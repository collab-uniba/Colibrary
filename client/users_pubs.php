<script type="text/javascript" src="http://view.jquery.com/tags/ui/latest/ui/ui.dialog.js"></script>
<script type="text/javascript" src="http://view.jquery.com/tags/ui/latest/ui/ui.resizable.js"></script>
<script type="text/javascript" src="http://view.jquery.com/tags/ui/latest/ui/ui.draggable.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/jquery/themes/flora/flora.dialog.css">  
<style type="text/css">

#dinamyc_box_user {
border:thin solid black;
left:785px;
position:relative;
top:-320px;
width:29%;
}

#p_hidden {
visibility:hidden;
font-style: oblique;
}

#users_info_i {
cursor:pointer;
}

#rdf_users {
cursor:pointer;
}

p {
 font-style: oblique;
-moz-opacity:	1;
opacity:1;
cursor:pointer;	
 }
 
a.bib {
color:red;
}

a.acm {
color:#0B479D;
}

a.cite {
color:green;
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
include_once("clean.php"); 
include_once("_params.php"); 
	
if(@$_SESSION['user_flag'])	{ ?>
<!--<table style="position:relative;left:508px">-->
<table style="position:relative;left:265px">
             <tr><td>
			 <span id="formInfo" title='<div>Clicking on blue users you will be redirected to profile page in Acm</div><br><div>Clicking on red user you will be redirected to profile page in Bibsonomy</div><br><div>Clicking on green user you will be redirected to profile page in Citeulike</div><br>'>
                <img src="img/info.gif" border="0" /></a>
	         </span></td>
			 <td><div ><a href="<?php echo RDF_REPOSITORY . $interhash . '_users.rdf'; ?>" target="_blank">
			 <img border="0" src="img/rdf_button.gif" /></a></div></td></tr>
			 <tr><td>&nbsp</td></tr>
</table>
<?php		
	ksort($users);
	foreach ($users as $nick => $link) {
		if (strpos($link, 'bibsonomy') != false) {
			$id = 'bib';
		} elseif (strpos($link, 'acm') != false) {
			$id = 'acm';
		} elseif (strpos($link, 'citeulike') != false) {
			$id = 'cite';
		}
		print '<a class=' . $id . ' target="_blank" href="' . $link . '">' . $nick . '</a>  ';		
	}
} else 
	print "No Users Found!"; 

?>

<!--<script type="text/javascript" src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.core.js"></script>-->
