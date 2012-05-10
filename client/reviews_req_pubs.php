 <?php
include_once("_params.php"); 
include_once(RDFAPI_INCLUDE_DIR . "RdfAPI.php"); 

//get interhash
if(isset($_GET['interhash'])) {
	if($_GET['interhash'] != NULL)
		$interhash = $_GET['interhash'];
}

//richiesta limitata alle review
$url_reviews = HOST . PORTA . "/Colibrary/publications/mashup/" . $interhash . "/social/reviews";

//if(isset($_GET['ReviewSource2']))
//	if ($_GET['ReviewSource2'] != 'all') 
//		$url_reviews .= '/' . $_GET['ReviewSource2'];
$model = ModelFactory::getDefaultModel();
$model->load($url_reviews);
$rdf_url = RDF_REPOSITORY . $interhash . "_reviews.rdf";
if (!$model->isEmpty()) $model->saveAs($rdf_url);

//navigate the model in the reviews' section
$r = new Resource(HOST . PORTA . "/Colibrary/publications/mashup/" . $interhash . "/social/reviews");
$res = $model->find($r, NULL, NULL);
if (!$res->isEmpty()) {
	// Get Iterator from model
	$it = $res->getStatementIterator();

	// Traverse model and output statements

	$array_rev = array();
	$array_user = array();
	$array_date = array();
	while ($it->hasNext()) { 
		$statement = $it->next();
		$uri = $statement->getLabelObject();
		$r = new Resource($uri);
		$rev_model = $model->find($r, null, null);
		if (!$rev_model->isEmpty()) {
			$rev_it = $rev_model->getStatementIterator();
			
			//Statement0 --> date
			$statement = $rev_it->next();
			$date = $statement->getLabelObject();
			array_push($array_date, $date);
			
			//Statement1 --> user
			$statement = $rev_it->next();
			$user = $statement->getLabelObject();
			array_push($array_user, $user);
			
			//Statement2 --> text
			$statement = $rev_it->next();
			$text = $statement->getLabelObject();
			array_push($array_rev, $text); 			
		} 	
	}

	if (count($array_rev)>0) {
		$_SESSION['rev_flag'] = true; 
	}
	// else {
		// $rev = array();
		// $source = array();
		// $users = array();
	// }
	
}

?>

<div id='content_reviews_req'>
<?php include("reviews_pubs.php"); ?>
</div>
 