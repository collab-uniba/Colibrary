 <?php
include_once("_params.php"); 
include_once(RDFAPI_INCLUDE_DIR . "RdfAPI.php"); 

/* $url_temporaneo= "http://".$server_name.$porta."/Colibrary/books/RDF_books_generator.php?isbn=";

$request_valid = true;

//verifica input e costruzione URL
$isbn = null;
 */
//get ISBN
if(isset($_GET['isbn'])) {
	if($_GET['isbn'] != NULL)
		$isbn = $_GET['isbn'];
//echo $isbn;
}

$url_tags = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/tags";

if(isset($_GET['TagSource']))
	if ($_GET['TagSource'] != 'both') 
		$url_tags .= '/source/' . $_GET['TagSource']; 
$model = ModelFactory::getDefaultModel();
$model->load($url_tags);
$rdf_url = "../Colibrary/rdf/" . $isbn . "_tags.rdf";
$rdf_url = RDF_REPOSITORY . $isbn . "_tags.rdf";
if (!$model->isEmpty()) $model->saveAs($rdf_url);
	
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
?>

<div id='content_tags_req'>
<?php include("tags.php"); ?>
</div>
 