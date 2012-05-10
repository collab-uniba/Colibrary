<?php
include_once("_params.php"); 
include_once(RDFAPI_INCLUDE_DIR . "RdfAPI.php"); 

//get interhash
if(isset($_GET['interhash'])) {
	if($_GET['interhash'] != NULL)
		$interhash = $_GET['interhash'];
}

//richiesta limitata a users
$url_users = HOST . PORTA . "/Colibrary/publications/mashup/" . $interhash . "/social/users";

//if(isset($_GET['UserSource2']))
//	if ($_GET['UserSource2'] != 'all') 
//		$url_users .= '/' . $_GET['UserSource2'];
$model = ModelFactory::getDefaultModel();
$model->load($url_users);
$rdf_url = RDF_REPOSITORY . $interhash . "_users.rdf";
if (!$model->isEmpty()) $model->saveAs($rdf_url);

//navigate the model in the users' section
$a_users = array();
$link = array();				
$r = new Resource(HOST . PORTA . "/Colibrary/publications/mashup/" . $interhash . "/social/users"); 
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
			array_push($a_users, $nick);		
			
			//Statement1 --> source
			$statement = $user_it->next();
			$source = $statement->getLabelObject();
			array_push($link, $source);
		} 
	}
}
		
if (count($a_users)>0) {
	$_SESSION['user_flag'] = true; 
	$users = array_combine($a_users, $link);
}
	
?>

<div id='content_users_req'>
<?php include("users_pubs.php"); ?>
</div>
 