<?php

	include("aws_signed_request.php");

	//--------------------------------------------------------------------------------------------------
	//Definizione funzioni
	 
	function getRDFBiblio($info_biblio, $isbn) {
		$search = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
		$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');		
		$s_fb = array('.', '\'', '"', ' ', 'è', 'é', 'à', 'á', 'ò', 'ó', 'ù', 'ú', 'ì', 'í');
		$r_fb = array('', '', '', '_', 'e', 'e', 'a', 'a', 'o', 'o', 'u', 'u', 'i', 'i');
		$model_biblio = new MemModel();
		$about = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/biblio";	
		$about = new Resource($about);
		if ($info_biblio->isbn != '') {
			$label = "http://www4.wiwiss.fu-berlin.de/bookmashup/doc/books/" . $info_biblio->isbn;
			$statement = new Statement($about, new Resource('http://purl.org/dc/elements/1.1/identifier'), new Literal($isbn));
			$model_biblio->add($statement);
		}
		if ($info_biblio->title != '') {
			$label = str_replace($search, $replace, $info_biblio->title);
			$statement = new Statement($about, new Resource('http://purl.org/dc/elements/1.1/title'), new Literal($label));
			$model_biblio->add($statement);
			$label_freebase = str_replace($s_fb, $r_fb, $info_biblio->title);
			if (strpos($label_freebase, ':') != false)
				$label_freebase = trim(substr($label_freebase, 0, strpos($label_freebase, ':')));
			if (strpos($label_freebase, '(') != false)
				$label_freebase = trim(substr($label_freebase, 0, strpos($label_freebase, '(') - 1));
			$statement_freebase_title = new Statement($about, new Resource('http://www.w3.org/2000/01/rdf-schema#seeAlso'), new Resource("http://rdf.freebase.com/ns/" . $label_freebase));				
		}	  
		if (@$info_biblio->authors)	{
			$lista = $info_biblio->authors;
			$label = "";
			$fb_authors = array();
			foreach ($lista->author as $a) {
				if ($a != "")	{
					$label .= $a . ", ";
					$label_freebase = str_replace($s_fb, $r_fb, $a);
					$statement_freebase_author = new Statement($about, new Resource('http://www.w3.org/2002/07/owl#sameAs'), new Resource("http://rdf.freebase.com/ns/" . $label_freebase));				
					array_push($fb_authors, $statement_freebase_author);
				}
			}
			$label = str_replace($search, $replace, $label);
			$statement = new Statement($about, new Resource('http://purl.org/dc/elements/1.1/creator'), new Literal(substr($label, 0, strlen($label) - 2))); 
			$model_biblio->add($statement);
		}
		if ($info_biblio->date != '') {
			$label = str_replace($search, $replace, $info_biblio->date);
			$statement = new Statement($about, new Resource('http://purl.org/dc/elements/1.1/date'), new Literal($label));
			$model_biblio->add($statement);
		}
		if ($info_biblio->publisher != '') {
			$label = str_replace($search, $replace, $info_biblio->publisher);
			$statement = new Statement($about, new Resource('http://purl.org/dc/elements/1.1/publisher'), new Literal($label));
			$model_biblio->add($statement);
		}
		if ($info_biblio->pages != '') {
			$label = str_replace($search, $replace, $info_biblio->pages);
			$statement = new Statement($about, new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'), new Literal($label));
			$model_biblio->add($statement);
		} 
		if ($info_biblio->cover != '') {
			$label = str_replace($search, $replace, $info_biblio->cover);
			$statement = new Statement($about, new Resource('http://xmlns.com/foaf/0.1/Image'), new Resource($label));
			$model_biblio->add($statement);
		}
		if ($info_biblio->link != '') {
			$label = str_replace($search, $replace, $info_biblio->link);
			$statement = new Statement($about, new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Resource($label));
			$model_biblio->add($statement);
		}   
		if ($info_biblio->isbn != '') {
			$label = "http://www4.wiwiss.fu-berlin.de/bookmashup/doc/books/" . $info_biblio->isbn;
			$statement = new Statement($about, new Resource('http://www.w3.org/2000/01/rdf-schema#seeAlso'), new Resource($label));
			$model_biblio->add($statement);
		}
		if (isset($statement_freebase_title)) $model_biblio->add($statement_freebase_title);
		if (count($fb_authors)>0)
			for ($i = 0; $i < count($fb_authors); $i++)
				$model_biblio->add($fb_authors[$i]);
		return $model_biblio;
	}
	
	function getRDFTag($total_tags, $total_occurs, $isbn) {
		$search = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
		$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');		
		$model_tag = new MemModel();
		$about_tags = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/tags";
		$about_tags = new Resource($about_tags);
		foreach ($total_tags as $label=>$ref) {
			if ($label != '') {
				$link = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/tags/" . str_replace("\"", "", trim($label));
				$label = str_replace($search, $replace, $link);
				$about_tag = new Resource($label);
				$statement = new Statement($about_tags, new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'), $about_tag); 
				$model_tag->add($statement);				
			}
		}
		foreach ($total_tags as $label=>$ref) {
			if ($label != '') {
				$l = str_replace($search, $replace, str_replace("\"", "", trim($label)));
				$link = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/tags/" . $l;
				$about_tag = new Resource($link);
				
				$statement = new Statement($about_tag, new Resource('http://moat-project.org/ns#Name'), new Literal($l)); 
				$model_tag->add($statement);
				$statement = new Statement($about_tag, new Resource('http://moat-project.org/ns#meaningURI'), new Resource('http://dbpedia.org/resource/' . trim($l))); 
				$model_tag->add($statement);
				$statement = new Statement($about_tag, new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/occurrence'), new Literal(strval(array_shift($total_occurs)))); 
				$model_tag->add($statement);
				$statement = new Statement($about_tag, new Resource('http://collab.di.uniba.it/colibrary/vocab/source'),new Literal($ref)); 
				$model_tag->add($statement);
				$s = array('\'', ' ', 'è', 'é', 'à', 'á', 'ò', 'ó', 'ù', 'ú', 'ì', 'í');
				$r = array('', '_', 'e', 'e', 'a', 'a', 'o', 'o', 'u', 'u', 'i', 'i');
				$label_freebase = str_replace($s, $r, $label);
				$statement = new Statement($about_tag, new Resource('http://www.w3.org/2000/01/rdf-schema#seeAlso'), new Resource("http://rdf.freebase.com/ns/" . $label_freebase));
				$model_tag->add($statement);
				$statement = new Statement($about_tag, new Resource('http://www.w3.org/2000/01/rdf-schema#seeAlso'), new Resource("http://sindice.com/query/lookup?keyword=" . $label . "&format=rdfxml"));
				$model_tag->add($statement);
				$statement = new Statement($about_tag, new Resource('http://www.w3.org/2000/01/rdf-schema#seeAlso'), new Resource("http://iws.seu.edu.cn/services/falcons/api/searchobjects.jsp?query=" . $label));
				$model_tag->add($statement);
			} 
		} 

		return $model_tag;
	} 

	function getRDFReview($array_rev, $array_users, $array_rat, $array_maxrat, $array_minrat, $isbn) {
		$search = array('&', '<', '>', '\'', '\"','’', 'à', 'è', 'é');  //special characters to be sostituted in the xml document
		$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'', '&agrave;', '&egrave;', '&eacute;');	
		$model_rev = new MemModel();
		$array_md5 = array();
		for ($i = 0; $i < count($array_users); $i++){
			$str = md5($array_users[$i] . $array_rev[$i]);
			array_push($array_md5, $str);
		} 
		$about_revs = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/reviews";
		$about_revs = new Resource($about_revs);
		foreach ($array_md5 as $id){
			$about_rev = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/reviews/" . $id;
			$about_rev = new Resource($about_rev);
			$statement = new Statement($about_revs, new Resource('http://purl.org/stuff/rev#hasReview'), $about_rev);
			$model_rev->add($statement); 	
		}
		for ($i = 0; $i < count($array_rev); $i++) {
			$label = str_replace($search, $replace, trim($array_rev[$i]));
			if ($label != '') {				
				$about_rev = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/reviews/" . array_shift($array_md5);
				$about_rev = new Resource($about_rev);
				$s1 = array('.', ',');  //special characters to be sostituted in the xml document
				$r1 = array('&#46;', '&#44;');	
				$statement = new Statement($about_rev, new Resource('http://purl.org/stuff/rev#reviewer'), new Literal(str_replace($s1, $r1, $array_users[$i]))); 
				$model_rev->add($statement);
				$statement = new Statement($about_rev, new Resource('http://purl.org/stuff/rev#text'),new Literal($label)); 
				$model_rev->add($statement);
				$statement = new Statement($about_rev, new Resource('http://purl.org/stuff/rev#minrating'),new Literal(str_replace(".", ",", $array_minrat[$i]))); 
				$model_rev->add($statement);
				$statement = new Statement($about_rev, new Resource('http://purl.org/stuff/rev#maxrating'),new Literal(str_replace(".", ",", $array_maxrat[$i]))); 
				$model_rev->add($statement);
				$statement = new Statement($about_rev, new Resource('http://purl.org/stuff/rev#rating'),new Literal(str_replace(".", ",", $array_rat[$i]))); 
				$model_rev->add($statement);
				
			}
		} 

		return $model_rev;
	} 
	
	function getRDFUser($array_users, $array_ref, $isbn) {
		$search = array('.', ',', '"');  //special characters to be sostituted in the xml document
		$replace = array('&#46;', '&#44;', '&#34;');	
		$model_user = new MemModel();
		$about_users = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/users";
		$about_users = new Resource($about_users);
		foreach ($array_users as $u){
			$about_user = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/users/" . str_replace($search, $replace, $u);
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users, new Resource('http://xmlns.com/foaf/0.1/Person'), $about_user);
			$model_user->add($statement); 	
		}
		for ($i = 0; $i < count($array_users); $i++) {
			$u = str_replace($search, $replace, $array_users[$i]);
			$about_user = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/users/" . $u;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_user, new Resource('http://xmlns.com/foaf/0.1/nick'), new Literal($u)); 
			$model_user->add($statement);
			$statement = new Statement($about_user, new Resource('http://xmlns.com/foaf/0.1/homepage'), new Literal($array_ref[$i])); 
			$model_user->add($statement);
		} 

		return $model_user;
	} 
	
	function getRDFHeader($isbn, $datatype, $socialtype, $socialdatasource){
		$model = getRDFNamespaces();
		
		$about_social = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social";
		$about_social = new Resource($about_social);
		
		//se è stata richiesta sia biblio sia social creo una description contenente le URI alle 2 parti
		if ($datatype == "both") {
			$about_header = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn;
			$about_header = new Resource($about_header);	
			$about_biblio = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/biblio";
			$about_biblio = new Resource($about_biblio);
			$statement = new Statement($about_header, new Resource('http://collab.di.uniba.it/colibrary/vocab/biblio'), $about_biblio); 
			@$model->add($statement);
			
			$statement = new Statement($about_header, new Resource('http://collab.di.uniba.it/colibrary/vocab/social'), $about_social); 
			@$model->add($statement);
		
		}
		//se è stata richiesta social creo una description contenente le URI di tags, users e reviews
		if ($datatype == "both" || $datatype == "social") {
			if ($socialtype == "all") {
				$about_tag = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/tags";
				$about_tag = new Resource($about_tag);
				$statement = new Statement($about_social, new Resource('http://collab.di.uniba.it/colibrary/vocab/hasTags'), $about_tag); 
				@$model->add($statement);
			}
			if ($socialtype == "all" || $socialtype == "reviews_users") {
				$about_user = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/users";
				$about_user = new Resource($about_user);
				$statement = new Statement($about_social, new Resource('http://collab.di.uniba.it/colibrary/vocab/hasUsers'), $about_user); 
				@$model->add($statement);
									
				$about_review = HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/social/reviews";
				$about_review = new Resource($about_review);
				$statement = new Statement($about_social, new Resource('http://collab.di.uniba.it/colibrary/vocab/hasReviews'), $about_review); 
				@$model->add($statement);				
			}
		}
		return $model;
	}
	
	function getRDFFilterByValue($model, $isbn, $datatype, $socialtype, $value) {
		$r = new Resource(HOST . PORTA . "/Colibrary/books/mashup/" . $isbn . "/" . $datatype . "/" . $socialtype . "/" . str_replace("%20", " ", $value));
		$newmodel = $model->find($r, NULL, NULL);	
		$newmodel->addModel(getRDFNamespaces());
		return $newmodel;
	}
	
	function getRDFNamespaces() {
		$model = new MemModel();

		//namespace non di default
		$model->addNamespace('coll','http://collab.di.uniba.it/colibrary/vocab/');
		$model->addNamespace('tag','http://www.holygoat.co.uk/owl/redwood/0.1/tags/');
		$model->addNamespace('foaf','http://xmlns.com/foaf/0.1/');
		$model->addNamespace('rev','http://purl.org/stuff/rev#');
		$model->addNamespace('moat','http://moat-project.org/ns#');
		return $model;
	}

	//Controlla su Amazon se l'isbn passato sia valido, se sì genera un file xml con le info bibliografiche
	function getBiblioByISBN($isbn) {
		$dom = new DOMDocument();
	
		//control the length of th code, it must be 10 or 13 characters (for the new ISBN-13 standard)
		if(strlen($isbn) == 10 || strlen($isbn) == 13) {       

/* modified by Domenico 25 august 2009 */                                                    
			
			/* The Amazon API key */
			/* $accesskey = '1DVFBDBX7NZEHVPSS102'; */

			$AWS_ACCESS_KEY_ID = "AKIAJVASKQ35MV7DT23A";

			$AWS_SECRET_KEY = "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";

			//$query = 'http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&SubscriptionId='.$accesskey.'&Operation=ItemLookup&ItemId='.$isbn.'&ResponseGroup=Large&ReviewSort=-HelpfulVotes'; 
       
			$dom = aws_signed_request("com", array("Operation"=>"ItemLookup","ItemId"=>$isbn,"ResponseGroup"=>"Large","ReviewSort"=>"-HelpfulVotes"), $AWS_ACCESS_KEY_ID, $AWS_SECRET_KEY);

			//$dom->formatOutput = TRUE;
			//$dom->loadXML($pxml);
			
			sleep(2);   //wait 2 seconds to avoid being throttled by Amazon API (only 1 query per second allowed)


			$xpath = new DOMXPath($dom);
			
			$errors = $xpath->query('//*[local-name()="Error"]');

			//if errors were encountered in the Amazon response
			if ($errors && $errors->length > 0) {   
				generate_error(1); 
				return false;
			} else {


				$search  = array('&', '<', '>', '\'', '\"');  //special characters to be sostituted in the xml document
				$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;');
				
				$isbn_dom = $dom->getElementsByTagName("ASIN"); //search in the DOM tree for the ASIN element				
				if($isbn_dom && $isbn_dom->length > 0)  //if exist (it must exist)
					$isbn_dom_value = $isbn_dom->item(0)->nodeValue;  //print it in the XML document
				
				$title_dom = $dom->getElementsByTagName("Title"); //search in the DOM tree for the Title element
				
				if($title_dom && $title_dom->length > 0)  //if exist
					$titolo = str_replace($search, $replace, $title_dom->item(0)->nodeValue);  //print it in the XML document 
				else
					$titolo = '';
					
				$author_dom = $dom->getElementsByTagName("Author"); //search in the DOM tree for the Author elements
				if($author_dom && $author_dom->length > 0) { //if exist
					$autore = "";
					for ($x=0; $x < $author_dom->length; $x++) {
						$autore = $autore . "\t<author>" . str_replace($search, $replace,$author_dom->item($x)->nodeValue) . "</author>";  //print it in the XML document   
					}  					
				}
				
				$pub_dom = $dom->getElementsByTagName("Manufacturer"); //search in the DOM tree for the Manufacturer element				
				if($pub_dom && $pub_dom->length > 0)  //if exist
					$publisher = str_replace($search, $replace,$pub_dom->item(0)->nodeValue);  //print it in the XML document  
				else
					$publisher = '';
					
				$date_dom = $dom->getElementsByTagName("PublicationDate"); //search in the DOM tree for the PublicationDate element			
				if($date_dom && $date_dom->length > 0)  //if exist
					$data = str_replace($search, $replace, $date_dom->item(0)->nodeValue);  //print it in the XML document     
				else
					$data = '';
					
				$page_dom = $dom->getElementsByTagName("NumberOfPages"); //search in the DOM tree for the NumberOfPages element
				if($page_dom && $page_dom->length > 0)  //if exist
					$pages = str_replace($search, $replace, $page_dom->item(0)->nodeValue);  //print it in the XML document    
				else
					$pages = '';
					
				$cover_dom = $dom->getElementsByTagName("MediumImage"); //search in the DOM tree for the MediumImage element
				if($cover_dom && $cover_dom->length > 0)  //if exist
					$cover = str_replace($search, $replace, $cover_dom->item(0)->firstChild->nodeValue);  //print it in the XML document       
				else
					$cover = '';
					
				$link_dom = $dom->getElementsByTagName("DetailPageURL"); //search in the DOM tree for the DetailPageURL element
				if($link_dom && $link_dom->length > 0)  //if exist
					$link = str_replace($search, $replace, $link_dom->item(0)->nodeValue);  //print it in the XML document          
				else
					$link = '';
					
				$info_xml = "info.xml";
				$header = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>";
				$root = "<post source=\"Amazon.com\">";
				$close_root = "</post>";
				$content = "<isbn>" . $isbn_dom_value . "</isbn>";
				$content .= "\n" . "<title>" . $titolo . "</title>";
				$content .= "\n" . "<authors>" . "\n" . $autore . "\n" . "</authors>";
				$content .= "\n" . "<publisher>" . $publisher . "</publisher>";
				$content .= "\n" . "<date>" . $data . "</date>";
				$content .= "\n" . "<pages>" . $pages . "</pages>";
				$content .= "\n" . "<cover>" . $cover . "</cover>";
				$content .= "\n" . "<link>" . $link . "</link>";
				$total = $header . "\n" . $root . "\n" . $content . "\n" . $close_root;
				$total = str_replace('\'', '&quot;', $total);
				$fp=fopen($info_xml,"w");
				fwrite($fp, $total);
				fclose($fp);
				return $total; //$info_xml;
			}
		
		}
		else {
			generate_error(1); 
			return false;
		}
	}

	//this function generates the xml code for the errors given the code of the error
	function generate_error($code){
		switch ($code) {
			case 1: 
				$message = "Invalid ISBN";           
				break;
			case 2: 
				$message = "Invalid parameters";           
				break;
		}
		$error_xml_code = "<error>\n <code>" . $code . "</code>\n"; 
		$error_xml_code .= " <message>" . $message . "</message>\n</error>\n";
		$error_file = "ErrorCode.xml";
		$total = "<errors>\n" . $error_xml_code. "</errors>\n";
		$total = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>\n" . $total;
		$fp = fopen($error_file, 'w');
		fwrite($fp, $total);
		fclose($fp); 
	}
	
	function getTags($page) {
		$simple = @simplexml_load_string($page);
		//estrazione tag
		$contat= 0;
		$tags = array();
		if (@$simple->tag)	{
			foreach ($simple->tag as $sim_t)	{
				$tags[$contat] = utf8_encode($sim_t);
				$contat++;
			}
		}
		return $tags;
	}

	function getOccurrences($page) {
		$simple = @simplexml_load_string($page);
		//estrazione occorrenze
		$occur = array();
		$contat= 0;
		if (@$simple->occurrences)	{
			foreach ($simple->occurrences as $sim_oc)	{
				$occur[$contat] = $sim_oc;
				$contat++;
			}
		}
		return $occur;
	}
	
	function getUsers($page) {
		$simple = @simplexml_load_string($page);
		//estrazione user
		$user = array();
		$contat= 0;
		if (@$simple->user)	{
			foreach ($simple->user as $sim_u)	{
				$user[$contat] = utf8_encode($sim_u);
				$contat++;
			}
		} 
		return $user;
	}
	
	function getReviews($page) {
		$simple = @simplexml_load_string($page);
		//estrazione review
		$review = array();
		$contat= 0;
		if (@$simple->review)	{
			foreach ($simple->review as $sim_r)	{
				$review[$contat] = $sim_r; //utf8_encode($sim_r);
				$contat++;
			}
		} 
		return $review; 
	}
	
	function getRatings($page) {
		$simple = @simplexml_load_string($page);
		//estrazione rating
		$rating = array();
		$contat= 0;
		if (@$simple->rating)	{
			foreach ($simple->rating as $sim_r)	{
				$rating[$contat] = $sim_r;
				$contat++;
			}
		} 
		return $rating; 
	}
	
	function getMaxRatings($page) {
		$simple = @simplexml_load_string($page);
		//estrazione max rating
		$maxrating = array();
		$contat= 0;
		if (@$simple->maxrating)	{
			foreach ($simple->maxrating as $sim_r)	{
				$maxrating[$contat] = $sim_r;
				$contat++;
			}
		} 
		return $maxrating; 
	}
	
	function getMinRatings($page) {
		$simple = @simplexml_load_string($page);
		//estrazione min rating
		$minrating = array();
		$contat= 0;
		if (@$simple->minrating)	{
			foreach ($simple->minrating as $sim_r)	{
				$minrating[$contat] = $sim_r;
				$contat++;
			}
		} 
		return $minrating; 
	}
		
?>