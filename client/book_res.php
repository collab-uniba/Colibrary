<?php  
	session_start();
?>


<html>
<head>

  <script src="http://code.jquery.com/jquery-latest.js"></script>

  <link rel="stylesheet" href="http://dev.jquery.com/view/tags/ui/latest/themes/flora/flora.all.css" type="text/css" media="screen" title="Flora (Default)">
  <script type="text/javascript" src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.core.js"></script>
  
  <script src="lib/jtip.js" type="text/javascript"></script>
  <script src="lib/getParams.js" type="text/javascript"></script>
    
  <link href="css/suggest.css" rel="stylesheet" type="text/css">
  <link href="css/body.css" rel="stylesheet" type="text/css">
  <link href="css/jTip.css" rel="stylesheet" type="text/css">
  <script src="lib/xml2json.js"></script>
  <script src="lib/suggest.js"></script>
  <script src="lib/suggestpubs.js"></script>
  
  <style>
	.no_cors{
		font-style:normal;
	}
	
	#toolbox {
		border:1px solid #000000;
		left:920px;
		padding:5px;
		position:absolute;
		top:100px;
		width:25%;
	}
   
	#colibrary_output2{
		float:right;
		width:40%;
	}
   
	<?php if(strpos($_SERVER['HTTP_USER_AGENT'],'Firefox')===FALSE){  ?>
   
	#colibrary_output{
		width:100%;
	}
	<?php }
	else{ ?>
	#colibrary_output{
		width:60%;
	}
	<?php }?>
   
	#li_tag {
		top:10px;   
	}
	
	li{
		margin-top:20px;
		cursor:default;
	}
	
	#box_bib {
		width: 98%;
		margin-bottom: 40px;
		margin-top: 10px;
		padding-left: 5px;
		padding-top: 5px;
		border: thin solid green;
	}
	
	#box_tag {
		width: 98%;
		margin-bottom: 40px;
		margin-top: 10px;
		padding-left: 5px;
		padding-top: 5px;
		border: thin solid green;

	}
	
	#box_rev {
		width: 98%;
		margin-bottom: 40px;
		margin-top: 10px;
		padding-left: 5px;
		padding-top: 5px;
		border: thin solid green;
		position:relative; 

	}
	#a_tit_t {
		width: 150px;
		margin-bottom: 5px;
		margin-top: 2px;
		padding-left: 4px;
		padding-right: 4px;
		padding-bottom: 4px;
		padding-top: 4px;
		border: thin solid green;
		background-color: #009900;
		cursor:pointer;
		color:white;
	}
	
	#a_tit_h {
		width: 210px;
		margin-bottom: 5px;
		margin-top: 2px;
		margin-left: 40px;
		padding-left: 4px;
		padding-right: 4px;
		padding-bottom: 4px;
		padding-top: 4px;
		border: thin solid green;
		background-color: #009900;
		cursor:pointer;
		color:white;
	}
	
	#a_tit_r {
		width: 150px;
		margin-bottom: 5px;
		margin-top: 2px;
		padding-left: 4px;
		padding-right: 4px;
		padding-bottom: 4px;
		padding-top: 4px;
		border: thin solid green;
		background-color: #009900;
		cursor:pointer;
		color:white;
	}
	
	#a_tit {
		width: 150px;
		margin-bottom: 5px;
		margin-top: 2px;
		padding-left: 4px;
		padding-right: 4px;
		padding-bottom: 4px;
		padding-top: 4px;
		border: thin solid green;
		background-color:#ff9900;
		cursor:pointer;
		color:white;
	}
	
	#a_tit:hover {		
		color:#99ff99;
	}
	
	#a_tit_h:hover {	
		color:#99ff99;
	}
	
	#a_tit_r:hover {	
		color:#99ff99;
	}
	
	#a_tit_t:hover {
		color:#99ff99;
	}
	
	#footer {
		position: relative;
		top: 20px;
		clear: both;
		margin: 10px;
		width: 98%;
		border: #000000 1px solid;
		padding: 5px;		
	}
	
</style>


<?php
//modificare per il server
include("_params.php"); 
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php"); 
error_reporting(0);
//adeguato alla convenzione REST
$url_temporaneo = HOST . PORTA . "/Colibrary/books/mashup/";

$request_valid = true;

//verifica input e costruzione URL
$isbn = null;
$data = null;
$social_source = null;
$social_type = null;
$tag_source = null;
$rev_source = null;
$tag_limit = null;

//get ISBN
if(isset($_GET['isbn'])) {
	if($_GET['isbn'] != NULL)
		$isbn = $_GET['isbn'];
}

//get the 'data' parameter
if(isset($_GET['data'])) {
	if($_GET['data'] != NULL) {
		if($_GET['data'] == 'biblio') {
			$data = 'biblio';
		}
		else if($_GET['data'] == 'social')
			$data = 'social';
    }
}

//get the SocialSource parameter
if(isset($_GET['SocialSource'])) {
	if($_GET['SocialSource'] != NULL) {
		if($_GET['SocialSource'] == 'amazon')
			$social_source = 'amazon';
		else if($_GET['SocialSource'] == 'librarything')
			$social_source = 'librarything';
		else if($_GET['SocialSource'] == 'anobii')
			$social_source = 'anobii';
    }
}

//get the SocialType parameter
if(isset($_GET['SocialType'])) {
	if($_GET['SocialType'] != NULL) {
		if($_GET['SocialType'] == 'tags')
			$social_type = 'tags';
		else if($_GET['SocialType'] == 'reviews')
			$social_type = 'reviews';
	}
}

//get the TagSource parameter
if(isset($_GET['TagSource'])) {
	if($_GET['TagSource'] != NULL) {
		if($_GET['TagSource'] == 'amazon')
			$tag_source = 'amazon';
		else if($_GET['TagSource'] == 'librarything')
			$tag_source = 'librarything';
		else if($_GET['TagSource'] == 'anobii')
			$tag_source = 'anobii';
		else
			$tag_source = 'all';
	}
}

//get the ReviewSource parameter
if(isset($_GET['ReviewSource'])) {
	if($_GET['ReviewSource'] != NULL) {
		if($_GET['ReviewSource'] == 'amazon')
			$rev_source = 'amazon';
		else if($_GET['ReviewSource'] == 'librarything')
			$rev_source = 'librarything';
		else if($_GET['ReviewSource'] == 'anobii')
			$rev_source = 'anobii';
		else
			$rev_source = 'all';
	}
}

//get TagLimit
if(isset($_GET['tag_limit'])) {
	if($_GET['tag_limit'] != NULL)
		$tag_limit = $_GET['tag_limit'];
}


$url = $url_temporaneo . $isbn;
	
$_SESSION['rev_flag']=false;
$_SESSION['tags_flag']=false;
$_SESSION['biblio_flag']=false;

$url_biblio = $url . "/biblio";
$model = ModelFactory::getDefaultModel();
@$model->load($url_biblio);

if ($model->isEmpty()) {
	$url_replace = HOST . PORTA . "/ColibraryClient/index.php";
	print '<script language="javascript"><!-- location.replace("' . $url_replace . '"); --></script>';
} 
else {
	$rdf_url = RDF_REPOSITORY . $isbn . "_biblio.rdf";
	@$model->saveAs($rdf_url);
}

$r = new Resource(HOST . PORTA . "/Colibrary/books/mashup/". $isbn . "/biblio");
$res = $model->find($r, NULL, NULL);
if($res->isEmpty()) {
	$request_valid = false;
}

$r = new Resource(HOST . PORTA . "/Colibrary/books/mashup/". $isbn . "/biblio");
$res = $model->find($r, NULL, NULL);

if($res->isEmpty()) {
	$request_valid = false;
}

if($request_valid) {

	$_SESSION['biblio_flag'] = true;	
	
	// Get Iterator from model
	$it = $res->getStatementIterator();

	while ($it->hasNext()) {
		$statement = $it->next();
		switch($statement->getLabelPredicate() ) {
			/* case 'http://purl.org/dc/elements/1.1/identifier':
				$isbn = $statement->getLabelObject();
				break; */
				
			case 'http://purl.org/dc/elements/1.1/title':
				$title = $statement->getLabelObject();
				break;
				
			case 'http://purl.org/dc/elements/1.1/creator':
				$authors = $statement->getLabelObject();
				break;
		
			case 'http://collab.di.uniba.it/colibrary/vocab/link':
				$amazon_link = $statement->getLabelObject();
				break;
		
			case 'http://purl.org/dc/elements/1.1/date':
				$pub_date = $statement->getLabelObject(); 
				break;
			
			case 'http://collab.di.uniba.it/colibrary/vocab/numberOfPages':
				$pages = $statement->getLabelObject(); 
				break;
				
			case 'http://purl.org/dc/elements/1.1/publisher':
				$publisher = $statement->getLabelObject(); 
				break;
				
			case 'http://xmlns.com/foaf/0.1/Image':
				$cover = $statement->getLabelObject(); 
				break;
		}
	}
	
	if ($data != 'biblio') {		
		$url .= '/social/'; 
		if ($social_type != 'reviews') {
			$url_tags = $url . "tags";
			if ($tag_source != 'all') 
				$url_tags .= '/source/' . $tag_source; 
			$model = ModelFactory::getDefaultModel();
			$model->load($url_tags);
			$rdf_url = RDF_REPOSITORY . $isbn . "_tags.rdf";
			if (!$model->isEmpty()) {
				$model->saveAs($rdf_url);
				
				$r = new Resource(HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/tags");
				$res = $model->find($r, NULL, NULL);

				if ($res->isEmpty()) {
					$url_replace = HOST . PORTA . "/ColibraryClient/index.php";
					print '<script language="javascript"><!-- location.replace("' . $url_replace . '"); --></script>';
				} 
				else {											
					$array_name = array();
					$array_occ = array();
					$array_source = array();
					
					// Get Iterator from model
					$it = $res->getStatementIterator();
					while ($it->hasNext()) { 
						$statement = $it->next();
						$uri = $statement->getLabelObject();
						$r = new Resource($uri);
						
						$tag_model = $model->find($r, null, null);
						if (!$tag_model->isEmpty()) {
							$tag_it = $tag_model->getStatementIterator();
							
							//Statement0 --> tag name
							$statement = $tag_it->next();
							$name = $statement->getLabelObject();
							array_push($array_name, $name);
							
							//Statement1 --> tag meaning
							$statement = $tag_it->next();
							$tagMeaning = $statement->getLabelObject();
							
							//Statement2 --> tag occurrence
							$statement = $tag_it->next();
							$tagOcc = $statement->getLabelObject();
							array_push($array_occ, $tagOcc);
							
							//Statement3 --> tag source
							$statement = $tag_it->next();
							$tagSource = $statement->getLabelObject();
							array_push($array_source, $tagSource);					
						}
					}
					if (count($array_name) > 0) {
						$tags = array_combine($array_name, $array_occ);
						$source = array_combine($array_name, $array_source);									
						$_SESSION['tags_flag'] = true; 
					}
					else {
						$tags = array();
						$source = array();
					}
				}
			} 
			else {
				$tags = array();
				$source = array();
			}
		}
		if ($social_type != 'tags') {
			//$_SESSION['rev_flag'] = true;
			$url_reviews = $url . "reviews/users";
			if ($rev_source != 'all') 
				$url_reviews .= '/source/' . $rev_source;
			$model = ModelFactory::getDefaultModel();
			$model->load($url_reviews);
			$rdf_url = RDF_REPOSITORY . $isbn . "_reviews.rdf";
			if (!$model->isEmpty()) {
				$model->saveAs($rdf_url);
			
				//navigate the model in the users' section
				$array_source = array();
				$array_nick = array();				
				$r = new Resource(HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/users"); 
				$user_model = $model->find($r, null, null);
				if (!$user_model->isEmpty()) {
					// Get Iterator from model
					$it = $user_model->getStatementIterator();
					// Traverse model and output statements
					while ($it->hasNext()) { 
						$statement = $it->next();
						$uri = $statement->getLabelObject();
						$r = new Resource($uri);
						$user_model = $model->find($r, null, null);
							
						if (!$user_model->isEmpty()) {
							$user_it = $user_model->getStatementIterator();
						
							//Statement0 --> nick
							$statement = $user_it->next();
							$nick = $statement->getLabelObject();
							array_push($array_nick, $nick);		
							
							//Statement1 --> source
							$statement = $user_it->next();
							$source = $statement->getLabelObject();
							array_push($array_source, $source);
						} 
					}
				}
						
				//navigate the model in the reviews' section
				$r = new Resource(HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/reviews");
				$res = $model->find($r, NULL, NULL);
				if (!$res->isEmpty()) {
					// Get Iterator from model
					$it = $res->getStatementIterator();

					// Traverse model and output statements

					$array_rev = array();
					$array_user = array();
					$array_minrating = array();
					$array_maxrating = array();
					$array_rating = array();
					while ($it->hasNext()) { 
						$statement = $it->next();
						$uri = $statement->getLabelObject();
						$r = new Resource($uri);
						$rev_model = $model->find($r, null, null);
						if (!$rev_model->isEmpty()) {
							$rev_it = $rev_model->getStatementIterator();
							
							//Statement0 --> user
							$statement = $rev_it->next();
							$user = $statement->getLabelObject();
							array_push($array_user, $user);
							
							//Statement1 --> text
							$statement = $rev_it->next();
							$text = $statement->getLabelObject();
							array_push($array_rev, $text);
							
							//Statement2 --> minrating
							$statement = $rev_it->next();
							$min = $statement->getLabelObject();
							array_push($array_minrating, $min);
							
							//Statement3 --> max rating
							$statement = $rev_it->next();
							$max = $statement->getLabelObject();
							array_push($array_maxrating, $max);
							
							//Statement4 --> rating
							$statement = $rev_it->next();
							$r = $statement->getLabelObject();
							array_push($array_rating, $r); 			
						} 
						else
							echo "URI: " . $uri . "<br/>";		
					}

					if (count($array_rev)>0) {
						$rev = array_combine($array_rev, $array_user);
						$source = array_combine($array_nick, $array_source);
						$mins = array_combine($array_rev, $array_minrating);
						$maxs = array_combine($array_rev, $array_maxrating);
						$ratings = array_combine($array_rev, $array_rating);
						$_SESSION['rev_flag'] = true; 
					}
					else {
						$rev = array();
						$source = array();
						$mins = array();
						$maxs = array();
						$ratings = array();
					}
				}
			} else {
				$rev = array();
				$source = array();
				$mins = array();
				$maxs = array();
				$ratings = array();
			}
		}
	}	
}
	
?>


<script type="text/javascript">
	function initMenus() {
	$('ul.menu ul').hide();
	$.each($('ul.menu'), function(){
		$('#' + this.id + '.expandfirst ul:first').show();
	});
	$('ul.menu li a').click(
		function() {
			var checkElement = $(this).next();
			var parent = this.parentNode.parentNode.id;

			if($('#' + parent).hasClass('noaccordion')) {
				$(this).next().slideToggle('normal');
				return false;
			}
			if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
				if($('#' + parent).hasClass('collapsible')) {
					$('#' + parent + ' ul:visible').slideUp('normal');
				}
				return false;
			}
			if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
				$('#' + parent + ' ul:visible').slideUp('normal');
				checkElement.slideDown('normal');
				return false;
			}
		}
	);
}


//------------------------------------------------------------------------end of function


$(document).ready(function() {

//crea il menu iniziale con click a scomparsa
initMenus();

var loading_tag = "Retrieving Tags...";
var loading_rev= "Retrieving Reviews...";
var get_isbn=$.getURLParam("isbn");

// carico con ajax i tag ed una volta ricevuti li mostro e li nascondo evitando di rifare la richiesta ajax

$('#a_tit_t').click(function(){
            if($(this).html()=='Search for Tags'){
			var loading = "<p align='center'><img src='img/ajax-loader.gif'> "+loading_tag+"</p>";
			$('#box_tag').html(loading);	
			$('#box_tag').load('tags_req.php?isbn='+get_isbn);
			$(this).css({ "background-color" : "#FF9900" });
			$(this).html('Tag Cloud');	
			var vis=true;
			}
			else if(vis){
			     $('#box_tag').hide();
				 vis=false;
				 }
				 else {
				 $('#box_tag').show();
				 vis=true;
				 }
} );	

// carico con ajax le rev ed una volta ricevuti li mostro e li nascondo senza rifare la richiesta
						           
$('#a_tit_r').click(function(){
            if($(this).html()=='Search for Reviews'){
            var loading = "<p align='center'><img src='img/ajax-loader.gif'> "+loading_rev+"</p>";
			$('#box_rev').html(loading);	
			$('#box_rev').load('reviews_req.php?isbn='+get_isbn);
			$(this).css({ "background-color" : "#FF9900" });
			$(this).html('Review List');
			var vis2=true;
			}
			else if(vis2){
			     $('#box_rev').hide();
				 vis2=false;
				 }
				 else {
				 $('#box_rev').show();
				 vis2=true;
				 }
} );	


			
});
	
</script>
</head>


<body OnKeyPress="return disableKeyPress(event)" background="img/sfondo.png" style="background-repeat: repeat-X; font-size:17px; font-family:Arial; font-weight:bold">
<center><h1 class="Stile1"><font size="15">COLIBRARY</font></h1></center>

<?php $home = HOST . PORTA . '/ColibraryClient';
?>

<div id="colibrary_output2" >
	<a href="<?php echo $home;?>" id='a_tit_h' ><< Back to Colibrary Home</a>


	<ul id="menu2" class="menu noaccordion ">
		
		<?php if($_SESSION['tags_flag']) {?>
		<li id='li_tag'>
			<a id='a_tit' href="#">Tag Cloud</a>
			<ul>
			<li><div id="box_tag"><?php include("tags.php"); ?></div></li>	
			</ul>
		</li>
		<?php } ?>
		
		
		
		
        <?php if (!$_SESSION['tags_flag']){ ?>
		<li id='li_tag'>
			<a id='a_tit_t' href="#">Search for Tags</a>
			<ul>
			<li><div id="box_tag"></div></li>
	    	</ul>
		</li>
		<?php }?>	

        
	</ul>	


</div> 

<div id="colibrary_output" >


	<ul id="menu" class="menu noaccordion expandfirst">
		<li>
			<a id='a_tit' href="#">Bibliographic information</a>
			<ul>
			<li><div id="box_bib"><?php if ($request_valid) include("biblio.php"); else print "Invalid ISBN";?></div></li>
			</ul>
		</li>
		
		
		<?php if($_SESSION['rev_flag']) {?>
		<li>
			<a id='a_tit' href="#">Review List</a>
			<ul>
			<li><div id="box_rev"><div id='content_reviews_req'><?php include("reviews.php"); ?></div></div></li>
			</ul>
		</li>
		<?php }?>
		
		
        	

        <?php if (!$_SESSION['rev_flag']){ ?>
		<li>
			<a id='a_tit_r' href="#">Search for Reviews</a>
			<ul>
			<li><div id="box_rev"></div></li>
        	</ul>
		</li>
		<?php }?>		
	</ul>	


</div> 



<br />





	<div id="footer" style="border: 1px #999999 solid; padding: 3px;">
		Colibrary Project - <a href="http://collab.di.uniba.it">Laboratorio di Ricerca per la Collaborazione in Rete</a> - Dipartimento di Informatica - Universita' di Bari
	</div>
</body>
</html>
