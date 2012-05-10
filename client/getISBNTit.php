<?php
	
	set_time_limit(0);

      // Set content type for XML
	header("Content-type: text/xml");
    
	$name = $_GET["testo"];

	/* The LibraryThing API*/
	$query = "http://www.librarything.com/api/thingTitle/" . $name;
	
	sleep(2);   //wait 2 seconds to avoid being throttled by Amazon API (only 1 query per second allowed)

	@$xmlisbn = simplexml_load_file($query);
	if ($xmlisbn) {
	
		$accesskey = "1DVFBDBX7NZEHVPSS102"; // The Amazon API key
		
		$hint = "";
	
		foreach($xmlisbn->isbn as $item){
			//sleep(2);   //wait 2 seconds to avoid being throttled by Amazon API (only 1 query per second allowed)
			$query = "http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&SubscriptionId=" . $accesskey;
			$query .= "&Operation=ItemLookup&ItemId=" . $item;
			$xmlinfo = simplexml_load_file($query);
			$hint .= "<book>";
			$hint .= "<isbn>" . htmlspecialchars($item) . "</isbn>";
			$hint .= "<author>" . htmlspecialchars($xmlinfo->Items->Item->ItemAttributes->Author) . "</author>";
			$hint .= "<manufacturer>" . htmlspecialchars($xmlinfo->Items->Item->ItemAttributes->Manufacturer) . "</manufacturer>";
			$hint .= "<title>" . htmlspecialchars($xmlinfo->Items->Item->ItemAttributes->Title) . "</title>";				
			$hint .= "<url>" . htmlspecialchars($xmlinfo->Items->Item->DetailPageURL) . "</url>";
			$hint .= "</book>";	
		}

		if ($hint == "") $response = "<book><isbn>No ISBNs found!</isbn></book>";
		else $response = $hint;
		
		$response = "<?xml version=\"1.0\" ?><books>" . $response . "</books>";		
	}
	else {
		$response = "<?xml version=\"1.0\" ?><books><book><isbn>No ISBNs found!</isbn></book></books>";		
	}
	
	echo $response;
	
?>