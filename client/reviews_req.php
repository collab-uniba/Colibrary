 <?php
include_once("_params.php"); 
include_once(RDFAPI_INCLUDE_DIR . "RdfAPI.php"); 

//get ISBN
if(isset($_GET['isbn'])) {
	if($_GET['isbn'] != NULL)
		$isbn = $_GET['isbn'];
    //echo $isbn;
}

//richiesta limitata alle review
$url_reviews = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/reviews/users";

if(isset($_GET['ReviewSource']))
	if ($_GET['ReviewSource'] != 'all') 
		$url_reviews .= '/source/' . $_GET['ReviewSource'];
$model = ModelFactory::getDefaultModel();
$model->load($url_reviews);
$rdf_url = RDF_REPOSITORY . $isbn . "_reviews.rdf";
if (!$model->isEmpty()) $model->saveAs($rdf_url);

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

?>

<div id='content_reviews_req'>
<?php include("reviews.php"); ?>
</div>
 