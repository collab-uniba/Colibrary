<?php
	include("aws_signed_request.php");
	header("Content-Type: text/xml");
	
	set_time_limit(0);
	
	$name = $_GET["testo"];
	
	//$accesskey = "1DVFBDBX7NZEHVPSS102"; /* The Amazon API key */

	//$query = "http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&SubscriptionId=" . $accesskey;
	//$query .= "&Operation=ItemSearch&SearchIndex=Books&Author=" . $name;


  

/* modified by Domenico 25 august 2009 */      
			$dom = new DOMDocument();                                              
			
			/* The Amazon API key */
			/* $accesskey = '1DVFBDBX7NZEHVPSS102'; */

			$AWS_ACCESS_KEY_ID = "AKIAJVASKQ35MV7DT23A";

			$AWS_SECRET_KEY = "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";

			//$query = 'http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&SubscriptionId='.$accesskey.'&Operation=ItemLookup&ItemId='.$isbn.'&ResponseGroup=Large&ReviewSort=-HelpfulVotes'; 
       
			$xml = aws_signed_request("com", array("Operation"=>"ItemSearch","Author"=>$name,"ResponseGroup"=>"Large","ReviewSort"=>"-HelpfulVotes"), $AWS_ACCESS_KEY_ID, $AWS_SECRET_KEY);
echo("ciao");
			echo($xml);
			//$dom->formatOutput = TRUE;
			//$dom->loadXML($pxml);
			
			sleep(2);   //wait 2 seconds to avoid being throttled by Amazon API (only 1 query per second allowed)

	
	//$xml = simplexml_load_file($query);
	
	$hint = "";

	foreach($xml->Items->Item as $item){
		$hint .= "<book>";		
		$hint .= "<isbn>" . htmlspecialchars($item->ASIN) . "</isbn>";
		$hint .= "<author>" . htmlspecialchars($item->ItemAttributes->Author) . "</author>";
		$hint .= "<manufacturer>" . htmlspecialchars($item->ItemAttributes->Manufacturer) . "</manufacturer>";
		$hint .= "<title>" . htmlspecialchars($item->ItemAttributes->Title) . "</title>";
		$hint .= "<url>" . htmlspecialchars($item->DetailPageURL) . "</url>";
		$hint .= "</book>";
	}

	if ($hint == "") $response = "<book><isbn>No ISBNs found!</isbn></book>";
	else $response = $hint;
	
	$response = "<?xml version=\"1.0\" ?><books>" . $response . "</books>";
	echo $response;	
?>
