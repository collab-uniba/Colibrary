<?php

function rdf_header($interhash, $hasTags, $hasUsers, $hasReviews){

	$model = new MemModel();

	//namespace non di default
	$model->addNamespace('coll','http://collab.di.uniba.it/colibrary/vocab/');
	$model->addNamespace('tag','http://www.holygoat.co.uk/owl/redwood/0.1/tags/');
	$model->addNamespace('foaf','http://xmlns.com/foaf/0.1/');
	$model->addNamespace('rev','http://purl.org/stuff/rev#');
	$model->addNamespace('moat','http://moat-project.org/ns#');

	$about_header = HOST.PORTA."/Colibrary/publications/mashup/".$interhash;
	$about_header = new Resource ($about_header);

	$about_biblio = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";
	$about_biblio = new Resource ($about_biblio);
	$statement1 = new Statement($about_header,new Resource('http://collab.di.uniba.it/colibrary/vocab/biblio'),$about_biblio); 
	$model->add($statement1);

	if ($hasTags || $hasUsers || $hasReviews) {
		$about_social = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social";
		$about_social = new Resource ($about_social);
		$statement2 = new Statement($about_header,new Resource('http://collab.di.uniba.it/colibrary/vocab/social'),$about_social); 
		$model->add($statement2);
		
		// rdf description che racchiude le URI per la sezione tag, review e user		
		if ($hasTags) {
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
			$about_tag = new Resource ($about_tag);
			$statement3 = new Statement($about_social,new Resource('http://collab.di.uniba.it/colibrary/vocab/hasTags'),$about_tag); 
			$model->add($statement3);
		}
		if ($hasReviews) {
			$about_review = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews";
			$about_review = new Resource ($about_review);
			$statement4 = new Statement($about_social,new Resource('http://collab.di.uniba.it/colibrary/vocab/hasReviews'),$about_review); 
			$model->add($statement4);
		}
		if ($hasUsers) {
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users";
			$about_user = new Resource ($about_user);
			$statement5 = new Statement($about_social,new Resource('http://collab.di.uniba.it/colibrary/vocab/hasUsers'),$about_user); 
			$model->add($statement5);
		}
	}
	return $model;

}//fine 
// rdf solo da bibsonomy
function rdf1($root_bib,$interhash,$total_lab_href,$total_lab_occ,$total_user_bibsonomy,$datatype,$socialtype,$socialdatatype){

	$search  = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
	$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');

	//dati da bibXML
	$bib_title=str_replace($search,$replace,$root_bib->title);
	$bib_authors=str_replace($search,$replace,$root_bib->authors);
	$bib_abstract=$root_bib->abst;
	$bib_editors=str_replace($search,$replace,$root_bib->editors);
	$bib_publisher=str_replace($search,$replace,$root_bib->publisher);
	$bib_journal=str_replace($search,$replace,$root_bib->journal);
	$bib_pages=str_replace($search,$replace,$root_bib->pages);
	$bib_year=str_replace($search,$replace,$root_bib->year);
	$bib_doi=str_replace($search,$replace,$root_bib->doi);
	$bib_isbn=str_replace($search,$replace,$root_bib->isbn);
	$bib_issn=str_replace($search,$replace,$root_bib->issn);
	$attr = $root_bib->bibsonomy_url->attributes();
	$bib_link=str_replace($search,$replace,$attr['href']);
	
	// inizio scrittura RDF
	$model = new MemModel();

	$about = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";
	$about = new Resource ($about);

	if ($bib_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($bib_title)); 
		$model->add($statement1);
	}	  
	if ($bib_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($bib_authors)); 
		$model->add($statement2);
	}
	if ($bib_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($bib_abstract)); 
		$model->add($statement3);
	}
	if ($bib_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($bib_journal));
		$model->add($statement11);
	}
	if ($bib_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($bib_pages));
		$model->add($statement12);
	} 
	if ($bib_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($bib_publisher));
		$model->add($statement4);
	}
	if ($bib_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($bib_editors));
		$model->add($statement5);
	}
	if ($bib_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($bib_year));
		$model->add($statement6);
	}
	if ($bib_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$bib_doi));
		$model->add($statement8);
	}
	if ($bib_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$bib_isbn)));
		$model->add($statement9);
	}
	if ($bib_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN',$bib_issn));
		$model->add($statement10);
	}
	if ($bib_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($bib_link));
		$model->add($statement13);
	}
	
	if (count($total_lab_occ)>0) {
		$about_tags = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
		$about_tags=new Resource($about_tags);

		foreach ($total_lab_occ as $label=>$occ){
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".$label;
			$about_tag = new Resource($about_tag);
			$statement = new Statement($about_tags,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'),$about_tag);
			$model->add($statement); 
		}//end foreach

		$model_tag=rdftag($total_lab_occ,$total_lab_href,$interhash);
		$model->addModel($model_tag);
		$hasTags = true;
	} else
		$hasTags = false;
	
	if (count($total_user_bibsonomy)>0) {
		$about_users=HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users";
		$about_users=new Resource($about_users);
			 
		foreach ($total_user_bibsonomy as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach

		$model_user=rdfuserbib($total_user_bibsonomy,$interhash);
		$model->addModel($model_user);
		$hasUsers = true;
	}  else
		$hasUsers = false;
		
	$hasReviews = false;
	$header=rdf_header($interhash, $hasTags, $hasUsers, $hasReviews);	  
	$header->addModel($model);
	$header->saveAs("o_".$interhash.".xml");
 
	filterrdf($datatype,$socialtype,$interhash,$socialdatatype);
	if ($datatype=='both'){
	header('Content-Type: text/xml');
	$data=file_get_contents('o_'.$interhash.'.xml');
	echo $data;
	unlink('o_'.$interhash.'.xml');
}

}//fine rdf1

function rdfuserbib($total_user_bibsonomy,$interhash){
	$model_user=new MemModel();
   
	foreach ($total_user_bibsonomy as $user) {       
		$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
		$about_user=new Resource($about_user);

		$statement1 = new Statement($about_user,new Resource('http://xmlns.com/foaf/0.1/nick'),new Literal($user)); 
		$model_user->add($statement1);

		$statement2 = new Statement($about_user,new Resource('http://collab.di.uniba.it/colibrary/vocab/source'),new Literal('http://www.bibsonomy.org/user/'.$user)); 
		$model_user->add($statement2);
	}
      
	return $model_user;
}//fine


function rdfusercite($total_user_citeulike,$interhash){
	$model_user=new MemModel();
   
	foreach ($total_user_citeulike as $user) {       
		$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
		$about_user=new Resource($about_user);

		$statement1 = new Statement($about_user,new Resource('http://xmlns.com/foaf/0.1/nick'),new Literal($user)); 
		$model_user->add($statement1);

		$statement2 = new Statement($about_user,new Resource('http://collab.di.uniba.it/colibrary/vocab/source'),new Literal('http://www.citeulike.org/user/'.$user)); 
		$model_user->add($statement2);
	} 
   
   return $model_user;
}//fine

function rdftag($total_lab_occ,$total_lab_href,$interhash){
   $model_tag=new MemModel();

   
   foreach ($total_lab_occ as $label=>$occ) {
		$occ=str_replace(' post','',$occ);
		$occ=str_replace('s','',$occ);
		$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".trim($label);
		$about_tag=new Resource($about_tag);
   
		$statement1 = new Statement($about_tag,new Resource('http://moat-project.org/ns#Name'),new Literal($label)); 
		$model_tag->add($statement1);
		$statement2 = new Statement($about_tag,new Resource('http://moat-project.org/ns#meaningURI'),new Resource('http://dbpedia.org/resource/'.ucfirst(trim($label)))); 
		$model_tag->add($statement2);
		$statement3 = new Statement($about_tag,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/occurrence'),new Literal($occ)); 
		$model_tag->add($statement3);
		$statement4 = new Statement($about_tag,new Resource('http://collab.di.uniba.it/colibrary/vocab/source'),new Literal(array_shift($total_lab_href))); 
		$model_tag->add($statement4);
	} 
   
   return $model_tag;
}// fine

function rdf2($root_cite,$interhash,$total_lab_href,$total_lab_occ,$total_user_citeulike,$array_rev,$datatype,$socialtype,$socialdatatype){ 
	// solo cite
	$search  = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
	$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');				         

	//dati da citeXML
	$cite_title=str_replace($search,$replace,$root_cite->title);
	$cite_authors=str_replace($search,$replace,$root_cite->authors);
	$cite_abstract=$root_cite->abst;
	$cite_editors=str_replace($search,$replace,$root_cite->editors);
	$cite_publisher=str_replace($search,$replace,$root_cite->publisher);
	$cite_journal=str_replace($search,$replace,$root_cite->journal);
	$cite_pages=str_replace($search,$replace,$root_cite->pages);
	$cite_year=str_replace($search,$replace,$root_cite->year);
	$cite_doi=str_replace($search,$replace,$root_cite->doi);
	$cite_isbn=str_replace($search,$replace,$root_cite->isbn);
	$cite_issn=str_replace($search,$replace,$root_cite->issn);
	$attr = $root_cite->citeulike_url->attributes();
	$cite_link=str_replace($search,$replace,$attr['href']);
	
	// inizio scrittura RDF
	$model = new MemModel();

	$about = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";	
	$about = new Resource ($about);

	if ($cite_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($cite_title)); 
		$model->add($statement1);
	}	  
	if ($cite_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($cite_authors)); 
		$model->add($statement2);
	}
	if ($cite_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($cite_abstract)); 
		$model->add($statement3);
	}
	if ($cite_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($cite_journal));
		$model->add($statement11);
	}
	if ($cite_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($cite_pages));
		$model->add($statement12);
	} 
	if ($cite_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($cite_publisher));
		$model->add($statement4);
	}
	if ($cite_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($cite_editors));
		$model->add($statement5);
	}
	if ($cite_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($cite_year));
		$model->add($statement6);
	}
	if ($cite_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$cite_doi));
		$model->add($statement8);
	}
	if ($cite_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$cite_isbn)));
		$model->add($statement9);
	}
	if ($cite_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN',$cite_issn));
		$model->add($statement10);
	}  
	if ($cite_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($cite_link));
		$model->add($statement13);
	}   
	  
	if (count($total_lab_occ)>0) {
		$about_tags = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
		$about_tags=new Resource($about_tags);

		foreach ($total_lab_occ as $label=>$occ){
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".$label;
			$about_tag = new Resource($about_tag);
			$statement = new Statement($about_tags,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'),$about_tag);
			$model->add($statement);
		}//end foreach
	   
		$model_tag=rdftag($total_lab_occ,$total_lab_href,$interhash);
		$model->addModel($model_tag);
		$hasTags = true;
	} else
		$hasTags = false;
		
	if (count($total_user_citeulike)>0) {
		$about_users=HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users";
		$about_users=new Resource($about_users);
			 
		foreach ($total_user_citeulike as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach
		  
		$model_user=rdfusercite($total_user_citeulike,$interhash);
		$model->addModel($model_user);
		$hasUsers = true;
	} else
		$hasUsers = false;
		
	if (count($array_rev)>0) {
		$array_rev_text=$array_rev["text"];
		$array_rev_date=$array_rev["date"];
		$array_rev_user=$array_rev["user"];
		$str='';
		$str_md5=array();
		for ($i=0; $i<count($array_rev_user); $i++){
			$str=md5($array_rev_user[$i].$array_rev_date[$i]);
			array_push($str_md5,$str);
		} 
		$about_revs = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews";
		$about_revs=new Resource($about_revs);
		foreach ($array_rev_user as $rev){
			$about_rev = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews/".array_shift($str_md5);
			$about_rev = new Resource($about_rev);
			$statement = new Statement($about_revs,new Resource('http://purl.org/stuff/rev#hasReview'),$about_rev);
			$model->add($statement); 	
		}//end foreach
		$model_rev=rdfrev($array_rev,$interhash);
		$model->addModel($model_rev);
		$hasReviews = true;
	} else
		$hasReviews = false;
	
	$header=rdf_header($interhash, $hasTags, $hasUsers, $hasReviews);	  
	$header->addModel($model);
	$header->saveAs("o_".$interhash.".xml");
	filterrdf($datatype,$socialtype,$interhash,$socialdatatype);
	if ($datatype=='both'){
		header('Content-Type: text/xml');
		$data=file_get_contents('o_'.$interhash.'.xml');
		echo $data;
		unlink('o_'.$interhash.'.xml');
	}

}// fine rdf2

function rdfrev($array_rev,$interhash){
	$model_rev=new MemModel();
      
    $array_rev_text=$array_rev["text"];
    $array_rev_date=$array_rev["date"];
    $array_rev_user=$array_rev["user"];
    //$str='';
    $str_md5=array();
    for ($i=0; $i<count($array_rev_user); $i++){
		$str=md5($array_rev_user[$i].$array_rev_date[$i]);
		array_push($str_md5,$str);
    } 
   
	$array_rev_text_date=array_combine($array_rev_text,$array_rev_date);
   
	foreach ($array_rev_text_date as $text=>$date) {
		$about_rev = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews/".array_shift($str_md5);
		$about_rev = new Resource($about_rev);
		$statement1 = new Statement($about_rev,new Resource('http://purl.org/stuff/rev#createdOn'),new Literal(str_replace('- ','',$date))); 
		$model_rev->add($statement1);
		$statement2 = new Statement($about_rev,new Resource('http://purl.org/stuff/rev#reviewer'),new Resource('http://www.citeulike.org/user/'.array_shift($array_rev_user))); 
		$model_rev->add($statement2);
		$statement3 = new Statement($about_rev,new Resource('http://purl.org/stuff/rev#text'),new Literal($text)); 
		$model_rev->add($statement3);
	}
	
	return $model_rev;
}//fine

function rdf3($root_acm,$interhash,$total_lab_href,$total_lab_occ,$datatype,$socialtype,$socialdatatype){ //solo acm
  
	$search  = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
	$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');

	//dati da acmXML
	$acm_title=str_replace($search,$replace,$root_acm->title);
	$acm_authors=str_replace($search,$replace,$root_acm->authors);
	$acm_abstract=$root_acm->abst;
	$acm_editors=str_replace($search,$replace,$root_acm->editors);
	$acm_publisher=str_replace($search,$replace,$root_acm->publisher);
	$acm_journal=str_replace($search,$replace,$root_acm->journal);
	$acm_pages=str_replace($search,$replace,$root_acm->pages);
	$acm_year=str_replace($search,$replace,$root_acm->year);
	$acm_doi=str_replace($search,$replace,$root_acm->doi);
	$acm_isbn=str_replace($search,$replace,$root_acm->isbn);
	$acm_issn=str_replace($search,$replace,$root_acm->issn);
	$attr = $root_acm->acm_url->attributes();
	$acm_link=str_replace($search,$replace,$attr['href']);
	
	// inizio scrittura RDF
	$model = new MemModel();

	$about = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";	
	$about = new Resource ($about);

	if ($acm_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($acm_title)); 
		$model->add($statement1);
	}	  
	if ($acm_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($acm_authors)); 
		$model->add($statement2);
	}
	if ($acm_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($acm_abstract)); 
		$model->add($statement3);
	}
	if ($acm_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($acm_journal));
		$model->add($statement11);
	} 
	if ($acm_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($acm_pages));
		$model->add($statement12);
	} 
	if ($acm_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($acm_publisher));
		$model->add($statement4);
	}
	if ($acm_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($acm_editors));
		$model->add($statement5);
	}
	if ($acm_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($acm_year));
		$model->add($statement6);
	}
	if ($acm_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$acm_doi));
		$model->add($statement8);
	}
	if ($acm_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$acm_isbn)));
		$model->add($statement9);
	}
	if ($acm_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN',$acm_issn));
		$model->add($statement10);
	}
	if ($acm_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($acm_link));
		$model->add($statement13);
	}
	
	if (count($total_lab_occ)>0) {
		$about_tags = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
		$about_tags=new Resource($about_tags);

		foreach ($total_lab_occ as $label=>$occ){
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".trim($label);
			$about_tag = new Resource($about_tag);
			$statement = new Statement($about_tags,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'),$about_tag);
			$model->add($statement); 
		}//end foreach
		$model_tag=rdftag($total_lab_occ,$total_lab_href,$interhash);
		$model->addModel($model_tag);	  
		$hasTags = true;
	} else
		$hasTags = false;
	
	$hasUsers = false;
	$hasReviews = false;
	$header=rdf_header($interhash, $hasTags, $hasUsers, $hasReviews);	  
	$header->addModel($model);
	$header->saveAs("o_".$interhash.".xml");
	filterrdf($datatype,$socialtype,$interhash,$socialdatatype);
	if ($datatype=='both'){
		header('Content-Type: text/xml; charset=ISO-8859-1');
		$data=file_get_contents('o_'.$interhash.'.xml');
		echo $data;
		unlink('o_'.$interhash.'.xml');
	}
}// fine rdf3


function rdf4($root_bib,$root_cite,$interhash,$total_lab_href,$total_lab_occ,$total_user_bibsonomy,$total_user_citeulike,$array_rev,$datatype,$socialtype,$socialdatatype) {

	$search  = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
	$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');
	//dati da bibXML
	$bib_title=str_replace($search,$replace,$root_bib->title);
	$bib_authors=str_replace($search,$replace,$root_bib->authors);
	$bib_abstract=$root_bib->abst;
	$bib_editors=str_replace($search,$replace,$root_bib->editors);
	$bib_publisher=str_replace($search,$replace,$root_bib->publisher);
	$bib_journal=str_replace($search,$replace,$root_bib->journal);
	$bib_pages=str_replace($search,$replace,$root_bib->pages);
	$bib_year=str_replace($search,$replace,$root_bib->year);
	$bib_doi=str_replace($search,$replace,$root_bib->doi);
	$bib_isbn=str_replace($search,$replace,$root_bib->isbn);
	$bib_issn=str_replace($search,$replace,$root_bib->issn);
	$attr = $root_bib->bibsonomy_url->attributes();
	$bib_link=str_replace($search,$replace,$attr['href']);
	
	//dati da citeXML
	$cite_title=str_replace($search,$replace,$root_cite->title);
	$cite_authors=str_replace($search,$replace,$root_cite->authors);
	$cite_abstract=$root_cite->abst;
	$cite_editors=str_replace($search,$replace,$root_cite->editors);
	$cite_publisher=str_replace($search,$replace,$root_cite->publisher);
	$cite_journal=str_replace($search,$replace,$root_cite->journal);
	$cite_pages=str_replace($search,$replace,$root_cite->pages);
	$cite_year=str_replace($search,$replace,$root_cite->year);
	$cite_doi=str_replace($search,$replace,$root_cite->doi);
	$cite_isbn=str_replace($search,$replace,$root_cite->isbn);
	$cite_issn=str_replace($search,$replace,$root_cite->issn);
	$attr = $root_cite->citeulike_url->attributes();
	$cite_link=str_replace($search,$replace,$attr['href']);
	
	// inizio scrittura RDF
	$model = new MemModel();

	$about = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";	
	$about = new Resource ($about);
	  
	if ($bib_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($bib_title)); 
		$model->add($statement1);
	} 
	else 	if ($cite_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($cite_title)); 
		$model->add($statement1);
	}   
	if ($bib_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($bib_authors)); 
		$model->add($statement2);
	}
	else 	if ($cite_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($cite_authors)); 
		$model->add($statement2);
	}   
	if ($bib_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($bib_abstract)); 
		$model->add($statement3);
	}
	else 	if ($cite_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($cite_abstract)); 
		$model->add($statement3);
	}
	if ($cite_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($cite_journal));
		$model->add($statement11);
	}
	else if ($bib_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($bib_journal));
		$model->add($statement11);
	} 
	if ($bib_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($bib_pages));
		$model->add($statement12);
	} 
	else if ($cite_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($cite_pages));
		$model->add($statement12);
	}  
	if ($bib_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($bib_publisher));
		$model->add($statement4);
	}
	else 	if ($cite_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($cite_publisher)); 
		$model->add($statement4);
	}   
	if ($bib_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($bib_editors));
		$model->add($statement5);
	}
	else 	if ($cite_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($cite_editors)); 
		$model->add($statement5);
	}   
	if ($bib_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($bib_year));
		$model->add($statement6);
	}
	else 	if ($cite_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($cite_year)); 
		$model->add($statement6);
	}   
	if ($bib_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$bib_doi));
		$model->add($statement8);
	}
	else 	if ($cite_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$cite_doi)); 
		$model->add($statement8);
	}   
	if ($bib_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$bib_isbn)));
		$model->add($statement9);
	}
	else 	if ($cite_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$cite_isbn))); 
		$model->add($statement9);
	}   
	if ($bib_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN:'.$bib_issn));
		$model->add($statement10);
	}
	else 	if ($cite_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Literal('urn:ISSN:'.$cite_issn)); 
		$model->add($statement10);
	}   
	if ($bib_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($bib_link));
		$model->add($statement13);
	}
	if ($cite_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($cite_link));
		$model->add($statement13);
	}
	
	if (count($total_lab_occ)>0) {
		$about_tags = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
		$about_tags=new Resource($about_tags);

		foreach ($total_lab_occ as $label=>$occ){
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".$label;
			$about_tag = new Resource($about_tag);
			$statement = new Statement($about_tags,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'),$about_tag);
			$model->add($statement); 
		}//end foreach
		  
		$model_tag=rdftag($total_lab_occ,$total_lab_href,$interhash);
		$model->addModel($model_tag);
		$hasTags = true;
	} else
		$hasTags = false;
		
	if (count($total_user_bibsonomy)>0 || count($total_user_citeulike)>0) {
		$about_users=HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users";
		$about_users=new Resource($about_users);
			 
		foreach ($total_user_bibsonomy as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach
		  
		foreach ($total_user_citeulike as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach
			
		$model_user=rdfuserbib($total_user_bibsonomy,$interhash);
		$model->addModel($model_user);

		$model_user1=rdfusercite($total_user_citeulike,$interhash);
		$model->addModel($model_user1);
		$hasUsers = true;
	} else
		$hasUsers = false;
		
	if (count($array_rev)>0) {
		$array_rev_text=$array_rev["text"];
		$array_rev_date=$array_rev["date"];
		$array_rev_user=$array_rev["user"];
		$str='';
		$str_md5=array();
		for ($i=0; $i<count($array_rev_user); $i++){
			$str=md5($array_rev_user[$i].$array_rev_date[$i]);
			array_push($str_md5,$str);
		} 
		$about_revs = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews";
		$about_revs=new Resource($about_revs);
		foreach ($array_rev_user as $rev){
			 $about_rev = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews/".array_shift($str_md5);
			 $about_rev = new Resource($about_rev);
			 $statement = new Statement($about_revs,new Resource('http://purl.org/stuff/rev#hasReview'),$about_rev);
			 $model->add($statement); 
		}//end foreach
		$model_rev=rdfrev($array_rev,$interhash);
		$model->addModel($model_rev);
		$hasReviews = true;
	} else
		$hasReviews = false;
	
	$header=rdf_header($interhash, $hasTags, $hasUsers, $hasReviews);	  
	$header->addModel($model);
	$header->saveAs("o_".$interhash.".xml");

	filterrdf($datatype,$socialtype,$interhash,$socialdatatype);
	if ($datatype=='both'){
		header('Content-Type: text/xml; charset=ISO-8859-1');
		$data=file_get_contents('o_'.$interhash.'.xml');
		echo $data;
		unlink('o_'.$interhash.'.xml');
	} 
}//fine rdf4

function rdf5($root_bib,$root_acm,$interhash,$total_lab_href,$total_lab_occ,$total_user_bibsonomy,$datatype,$socialtype,$socialdatatype){

	$search  = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
	$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');

	//dati da bibXML
	$bib_title=str_replace($search,$replace,$root_bib->title);
	$bib_authors=str_replace($search,$replace,$root_bib->authors);
	$bib_abstract=$root_bib->abst;
	$bib_editors=str_replace($search,$replace,$root_bib->editors);
	$bib_publisher=str_replace($search,$replace,$root_bib->publisher);
	$bib_journal=str_replace($search,$replace,$root_bib->journal);
	$bib_pages=str_replace($search,$replace,$root_bib->pages);
	$bib_year=str_replace($search,$replace,$root_bib->year);
	$bib_doi=str_replace($search,$replace,$root_bib->doi);
	$bib_isbn=str_replace($search,$replace,$root_bib->isbn);
	$bib_issn=str_replace($search,$replace,$root_bib->issn);
	$attr = $root_bib->bibsonomy_url->attributes();
	$bib_link=str_replace($search,$replace,$attr['href']);
	
	//dati da acmXML
	$acm_title=str_replace($search,$replace,$root_acm->title);
	$acm_authors=str_replace($search,$replace,$root_acm->authors);
	$acm_abstract=$root_acm->abst;
	$acm_editors=str_replace($search,$replace,$root_acm->editors);
	$acm_publisher=str_replace($search,$replace,$root_acm->publisher);
	$acm_journal=str_replace($search,$replace,$root_acm->journal);
	$acm_pages=str_replace($search,$replace,$root_acm->pages);
	$acm_year=str_replace($search,$replace,$root_acm->year);
	$acm_doi=str_replace($search,$replace,$root_acm->doi);
	$acm_isbn=str_replace($search,$replace,$root_acm->isbn);
	$acm_issn=str_replace($search,$replace,$root_acm->issn);
	$attr = $root_acm->acm_url->attributes();
	$acm_link=str_replace($search,$replace,$attr['href']);

	// inizio scrittura RDF
	$model = new MemModel();

	$about = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";	
	$about = new Resource ($about);
	  
	if ($bib_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($bib_title)); 
		$model->add($statement1);
	} 
	else 	if ($acm_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($acm_title)); 
		$model->add($statement1);
	}   
	if ($bib_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($bib_authors)); 
		$model->add($statement2);
	}
	else 	if ($acm_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($acm_authors)); 
		$model->add($statement2);
	}   
	if ($bib_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($bib_abstract)); 
		$model->add($statement3);
	}
	else 	if ($acm_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($acm_abstract)); 
		$model->add($statement3);
	}
	if ($acm_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($acm_journal));
		$model->add($statement11);
	}
	else if ($bib_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($bib_journal));
		$model->add($statement11);
	} 
	if ($bib_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($bib_pages));
		$model->add($statement12);
	} 
	else if ($acm_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($acm_pages));
		$model->add($statement12);
	}     
	if ($bib_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($bib_publisher));
		$model->add($statement4);
	}
	else 	if ($acm_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($acm_publisher)); 
		$model->add($statement4);
	}   
	if ($bib_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($bib_editors));
		$model->add($statement5);
	}
	else 	if ($acm_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($acm_editors)); 
		$model->add($statement5);
	}   
	if ($bib_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($bib_year));
		$model->add($statement6);
	}
	else 	if ($acm_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($acm_year)); 
		$model->add($statement6);
	}   
	if ($acm_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$acm_doi)); 
		$model->add($statement8);
	}   
	else if ($bib_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$bib_doi));
		$model->add($statement8);
	}	
	if ($acm_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$acm_isbn))); 
		$model->add($statement9);
	}   
	else if ($bib_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$bib_isbn)));
		$model->add($statement9);
	}	
	if ($acm_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Literal('urn:ISSN:'.$acm_issn)); 
		$model->add($statement10);
	}   
	else if ($bib_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN:'.$bib_issn));
		$model->add($statement10);
	}	
	if ($bib_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($bib_link));
		$model->add($statement13);
	}
	if ($acm_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($acm_link));
		$model->add($statement13);
	}

	if (count($total_lab_occ)>0) {
		$about_tags = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
		$about_tags=new Resource($about_tags);

		foreach ($total_lab_occ as $label=>$occ){
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".trim($label);
			$about_tag = new Resource($about_tag);
			$statement = new Statement($about_tags,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'),$about_tag);
			$model->add($statement); 
		}//end foreach
		  
		$model_tag=rdftag($total_lab_occ,$total_lab_href,$interhash);
		$model->addModel($model_tag);
		$hasTags = true;
	} else
		$hasTags = false;
		
	if (count($total_user_bibsonomy)>0) {
		$about_users=HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users";
		$about_users=new Resource($about_users);

		foreach ($total_user_bibsonomy as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach
			  
		$model_user=rdfuserbib($total_user_bibsonomy,$interhash);
		$model->addModel($model_user);
		$hasUsers = true;
	} else
		$hasUsers = false;
		
	$hasReviews = false;
	$header=rdf_header($interhash, $hasTags, $hasUsers, $hasReviews);	  
	$header->addModel($model);

	$header->saveAs("o_".$interhash.".xml");
	filterrdf($datatype,$socialtype,$interhash,$socialdatatype);
	if ($datatype=='both'){
		header('Content-Type: text/xml; charset=ISO-8859-1');
		$data=file_get_contents('o_'.$interhash.'.xml');
		echo $data;
		unlink('o_'.$interhash.'.xml');
	}
}// fine rdf5

function rdf6($root_cite,$root_acm,$interhash,$total_lab_href,$total_lab_occ,$total_user_citeulike,$array_rev,$datatype,$socialtype,$socialdatatype){

	$search  = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
	$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');

	//dati da citeXML
	$cite_title=str_replace($search,$replace,$root_cite->title);
	$cite_authors=str_replace($search,$replace,$root_cite->authors);
	$cite_abstract=$root_cite->abst;
	$cite_editors=str_replace($search,$replace,$root_cite->editors);
	$cite_publisher=str_replace($search,$replace,$root_cite->publisher);
	$cite_journal=str_replace($search,$replace,$root_cite->journal);
	$cite_pages=str_replace($search,$replace,$root_cite->pages);
	$cite_year=str_replace($search,$replace,$root_cite->year);
	$cite_doi=str_replace($search,$replace,$root_cite->doi);
	$cite_isbn=str_replace($search,$replace,$root_cite->isbn);
	$cite_issn=str_replace($search,$replace,$root_cite->issn);
	$attr = $root_cite->citeulike_url->attributes();
	$cite_link=str_replace($search,$replace,$attr['href']);

	//dati da acmXML
	$acm_title=str_replace($search,$replace,$root_acm->title);
	$acm_authors=str_replace($search,$replace,$root_acm->authors);
	$acm_abstract=$root_acm->abst;
	$acm_editors=str_replace($search,$replace,$root_acm->editors);
	$acm_publisher=str_replace($search,$replace,$root_acm->publisher);
	$acm_journal=str_replace($search,$replace,$root_acm->journal);
	$acm_pages=str_replace($search,$replace,$root_acm->pages);
	$acm_year=str_replace($search,$replace,$root_acm->year);
	$acm_doi=str_replace($search,$replace,$root_acm->doi);
	$acm_isbn=str_replace($search,$replace,$root_acm->isbn);
	$acm_issn=str_replace($search,$replace,$root_acm->issn);
	$attr = $root_acm->acm_url->attributes();
	$acm_link=str_replace($search,$replace,$attr['href']);

	// inizio scrittura RDF
	$model = new MemModel();

	$about = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";	
	$about = new Resource ($about);
	  
	if ($cite_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($cite_title)); 
		$model->add($statement1);
	} 
	else 	if ($acm_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($acm_title)); 
		$model->add($statement1);
	}   
	if ($cite_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($cite_authors)); 
		$model->add($statement2);
	}
	else 	if ($acm_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($acm_authors)); 
		$model->add($statement2);
	}   
	if ($cite_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($cite_abstract)); 
		$model->add($statement3);
	}
	else 	if ($acm_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($acm_abstract)); 
		$model->add($statement3);
	}
	if ($acm_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($acm_journal));
		$model->add($statement11);
	}  
	else if ($cite_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($cite_journal));
		$model->add($statement11);
	} 
	if ($cite_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($cite_pages));
		$model->add($statement12);
	} 
	else if ($acm_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($acm_pages));
		$model->add($statement12);
	}    
	if ($cite_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($cite_publisher));
		$model->add($statement4);
	}
	else 	if ($acm_publisher!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($acm_publisher)); 
		$model->add($statement5);
	}   
	if ($cite_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($cite_editors));
		$model->add($statement5);
	}
	else 	if ($acm_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($acm_editors)); 
		$model->add($statement5);
	}   
	if ($cite_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($cite_year));
		$model->add($statement6);
	}
	else 	if ($acm_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($acm_year)); 
		$model->add($statement6);
	}   
	if ($acm_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$acm_doi)); 
		$model->add($statement8);
	}   
	else if ($cite_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$cite_doi));
		$model->add($statement8);
	}	
	if ($acm_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$acm_isbn))); 
		$model->add($statement9);
	}   
	else if ($cite_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$cite_isbn)));
		$model->add($statement9);
	}	
	if ($acm_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Literal('urn:ISSN:'.$acm_issn)); 
		$model->add($statement10);
	}   
	else if ($cite_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN:'.$cite_issn));
		$model->add($statement10);
	}	
	if ($cite_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($cite_link));
		$model->add($statement13);
	}
	if ($acm_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($acm_link));
		$model->add($statement13);
	}
	
	if (count($total_lab_occ)>0) {
		$about_tags = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
		$about_tags=new Resource($about_tags);

		foreach ($total_lab_occ as $label=>$occ){
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".trim($label);
			$about_tag = new Resource($about_tag);
			$statement = new Statement($about_tags,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'),$about_tag);
			$model->add($statement); 
		}//end foreach
		  
		$model_tag=rdftag($total_lab_occ,$total_lab_href,$interhash);
		$model->addModel($model_tag);
		$hasTags = true;
	} else
		$hasTags = false;
		
	if (count($total_user_citeulike)>0) {
		$about_users=HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users";
		$about_users=new Resource($about_users);

		foreach ($total_user_citeulike as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach
		  
		$model_user=rdfusercite($total_user_citeulike,$interhash);
		$model->addModel($model_user);
		$hasUsers = true;
	} else
		$hasUsers = false;

	if (count($array_rev)>0) {
		$array_rev_text=$array_rev["text"];
		$array_rev_date=$array_rev["date"];
		$array_rev_user=$array_rev["user"];
		$str='';
		$str_md5=array();
		for ($i=0; $i<count($array_rev_user); $i++){
			$str=md5($array_rev_user[$i].$array_rev_date[$i]);
			array_push($str_md5,$str);
		} 
		$about_revs = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews";
		$about_revs=new Resource($about_revs);
		foreach ($array_rev_user as $rev){
			$about_rev = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews/".array_shift($str_md5);
			$about_rev = new Resource($about_rev);
			$statement = new Statement($about_revs,new Resource('http://purl.org/stuff/rev#hasReview'),$about_rev);
			$model->add($statement); 	
		}//end foreach
		$model_rev=rdfrev($array_rev,$interhash);
		$model->addModel($model_rev);
		$hasReviews = true;
	} else
		$hasReviews = false;
	$header=rdf_header($interhash, $hasTags, $hasUsers, $hasReviews);	  
	$header->addModel($model);
	$header->saveAs("o_".$interhash.".xml");
	filterrdf($datatype,$socialtype,$interhash,$socialdatatype);
	if ($datatype=='both'){
		header('Content-Type: text/xml; charset=ISO-8859-1');
		$data=file_get_contents('o_'.$interhash.'.xml');
		echo $data;
		unlink('o_'.$interhash.'.xml');
	}
}// fine rdf6

function rdf7($root_bib,$root_cite,$root_acm,$interhash,$total_lab_href,$total_lab_occ,$total_user_bibsonomy,$total_user_citeulike,$array_rev,$datatype,$socialtype,$socialdatatype){

	$search  = array('&', '<', '>', '\'', '\"','’');  //special characters to be sostituted in the xml document
	$replace = array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;','\'');

	//dati da bibXML
	$bib_title=str_replace($search,$replace,$root_bib->title);
	$bib_authors=str_replace($search,$replace,$root_bib->authors);
	$bib_abstract=$root_bib->abst;
	$bib_editors=str_replace($search,$replace,$root_bib->editors);
	$bib_publisher=str_replace($search,$replace,$root_bib->publisher);
	$bib_journal=str_replace($search,$replace,$root_bib->journal);
	$bib_pages=str_replace($search,$replace,$root_bib->pages);
	$bib_year=str_replace($search,$replace,$root_bib->year);
	$bib_doi=str_replace($search,$replace,$root_bib->doi);
	$bib_isbn=str_replace($search,$replace,$root_bib->isbn);
	$bib_issn=str_replace($search,$replace,$root_bib->issn);
	$attr = $root_bib->bibsonomy_url->attributes();
	$bib_link=str_replace($search,$replace,$attr['href']);
	
	//dati da citeXML
	$cite_title=str_replace($search,$replace,$root_cite->title);
	$cite_authors=str_replace($search,$replace,$root_cite->authors);
	$cite_abstract=$root_cite->abst;
	$cite_editors=str_replace($search,$replace,$root_cite->editors);
	$cite_publisher=str_replace($search,$replace,$root_cite->publisher);
	$cite_journal=str_replace($search,$replace,$root_cite->journal);
	$cite_pages=str_replace($search,$replace,$root_cite->pages);
	$cite_year=str_replace($search,$replace,$root_cite->year);
	$cite_doi=str_replace($search,$replace,$root_cite->doi);
	$cite_isbn=str_replace($search,$replace,$root_cite->isbn);
	$cite_issn=str_replace($search,$replace,$root_cite->issn);
	$attr = $root_cite->citeulike_url->attributes();
	$cite_link=str_replace($search,$replace,$attr['href']);
	
	//dati da acmXML
	$acm_title=str_replace($search,$replace,$root_acm->title);
	$acm_authors=str_replace($search,$replace,$root_acm->authors);
	$acm_abstract=$root_acm->abst;
	$acm_editors=str_replace($search,$replace,$root_acm->editors);
	$acm_publisher=str_replace($search,$replace,$root_acm->publisher);
	$acm_journal=str_replace($search,$replace,$root_acm->journal);
	$acm_pages=str_replace($search,$replace,$root_acm->pages);
	$acm_year=str_replace($search,$replace,$root_acm->year);
	$acm_doi=str_replace($search,$replace,$root_acm->doi);
	$acm_isbn=str_replace($search,$replace,$root_acm->isbn);
	$acm_issn=str_replace($search,$replace,$root_acm->issn);
	$attr = $root_acm->acm_url->attributes();
	$acm_link=str_replace($search,$replace,$attr['href']);
	
	// inizio scrittura RDF
	$model = new MemModel();

	$about = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio";	
	$about = new Resource ($about);
	  
	if ($bib_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($bib_title)); 
		$model->add($statement1);
	}	  
	else if ($cite_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($cite_title)); 
		$model->add($statement1);
	} 
	else if ($acm_title!='') {
		$statement1 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/title'),new Literal($acm_title)); 
		$model->add($statement1);
	}

	if ($bib_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($bib_authors)); 
		$model->add($statement2);
	}    
	else if ($cite_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($cite_authors)); 
		$model->add($statement2);
	}
	else 	if ($acm_authors!='') {
		$statement2 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/creator'),new Literal($acm_authors)); 
		$model->add($statement2);
	}

	if ($bib_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($bib_abstract)); 
		$model->add($statement3);
	}   
	else if ($cite_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($cite_abstract)); 
		$model->add($statement3);
	}
	else 	if ($acm_abstract!='') {
		$statement3 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/description'),new Literal($acm_abstract)); 
		$model->add($statement3);
	}
	if ($acm_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($acm_journal));
		$model->add($statement11);
	} 
	elseif ($cite_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($cite_journal));
		$model->add($statement11);
	}  
	else if ($bib_journal!='') {
		$statement11 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/journal'),new Literal($bib_journal));
		$model->add($statement11);
	} 
	if ($bib_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($bib_pages));
		$model->add($statement12);
	} 
	else if ($cite_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($cite_pages));
		$model->add($statement12);
	}  
	else if ($acm_pages!='') {
		$statement12 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/numberOfPages'),new Literal($acm_pages));
		$model->add($statement12);
	} 
	 
	if ($bib_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($bib_publisher));
		$model->add($statement4);
	}   
	else if ($cite_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($cite_publisher));
		$model->add($statement4);
	}
	else 	if ($acm_publisher!='') {
		$statement4 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/publisher'),new Literal($acm_publisher)); 
		$model->add($statement4);
	}

	if ($bib_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($bib_editors));
		$model->add($statement5);
	}   
	else if ($cite_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($cite_editors));
		$model->add($statement5);
	}
	else if ($acm_editors!='') {
		$statement5 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/contributor'),new Literal($acm_editors)); 
		$model->add($statement5);
	}

	if ($bib_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($bib_year));
		$model->add($statement6);
	}   
	else if ($cite_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($cite_year));
		$model->add($statement6);
	}
	else 	if ($acm_year!='') {
		$statement6 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/date'),new Literal($acm_year)); 
		$model->add($statement6);
	}

	if ($acm_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$acm_doi)); 
		$model->add($statement8);
	}
	else if ($bib_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$bib_doi));
		$model->add($statement8);
	}   
	else if ($cite_doi!='') {
		$statement8 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:DOI:'.$cite_doi));
		$model->add($statement8);
	}	 

	if ($acm_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$acm_isbn))); 
		$model->add($statement9);
	}  
	else if ($cite_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$cite_isbn)));
		$model->add($statement9);
	}
	else if ($bib_isbn!='') {
		$statement9 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISBN:http://www4.wiwiss.fu-berlin.de/bookmashup/books/'.str_replace('-','',$bib_isbn)));
		$model->add($statement9);
	} 	

	if ($acm_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN:'.$acm_issn)); 
		$model->add($statement10);
	}  
	else if ($cite_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN:'.$cite_issn));
		$model->add($statement10);
	}
	else if ($bib_issn!='') {
		$statement10 = new Statement($about,new Resource('http://purl.org/dc/elements/1.1/identifier'),new Resource('urn:ISSN:'.$bib_issn));
		$model->add($statement10);
	} 	
	if ($bib_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($bib_link));
		$model->add($statement13);
	}  
	if ($cite_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($cite_link));
		$model->add($statement13);
	}  
	if ($acm_link!='') {
		$statement13 = new Statement($about,new Resource('http://collab.di.uniba.it/colibrary/vocab/link'),new Literal($acm_link));
		$model->add($statement13);
	}  

	if (count($total_lab_occ)>0) {
		$about_tags = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags";
		$about_tags=new Resource($about_tags);

		foreach ($total_lab_occ as $label=>$occ){
			$about_tag = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/".trim($label);
			$about_tag = new Resource($about_tag);
			$statement = new Statement($about_tags,new Resource('http://www.holygoat.co.uk/owl/redwood/0.1/tags/tag'),$about_tag);
			$model->add($statement); 
		}//end foreach
		  
		$model_tag=rdftag($total_lab_occ,$total_lab_href,$interhash);
		$model->addModel($model_tag);
		$hasTags = true;
	} else
		$hasTags = false;
		
	if (count($total_user_bibsonomy)>0 || count($total_user_citeulike)>0) {
		$about_users=HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users";
		$about_users=new Resource($about_users);
			 
		foreach ($total_user_bibsonomy as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach
				 
		foreach ($total_user_citeulike as $user){
			$about_user = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/".$user;
			$about_user = new Resource($about_user);
			$statement = new Statement($about_users,new Resource('http://xmlns.com/foaf/0.1/Person'),$about_user);
			$model->add($statement); 
		}//end foreach
		  
		$model_user=rdfuserbib($total_user_bibsonomy,$interhash);
		$model->addModel($model_user);
		
		$model_user1=rdfusercite($total_user_citeulike,$interhash);
		$model->addModel($model_user1);
		$hasUsers = true;
	} else
		$hasUsers = false;
	
	if (count($array_rev)>0) {
		$array_rev_text=$array_rev["text"];
		$array_rev_date=$array_rev["date"];
		$array_rev_user=$array_rev["user"];
		$str='';
		$str_md5=array();
		for ($i=0; $i<count($array_rev_user); $i++){
			$str=md5($array_rev_user[$i].$array_rev_date[$i]);
			array_push($str_md5,$str);
		} 
		$about_revs = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews";
		$about_revs=new Resource($about_revs);
		foreach ($array_rev_user as $rev){
			$about_rev = HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews/".array_shift($str_md5);
			$about_rev = new Resource($about_rev);
			$statement = new Statement($about_revs,new Resource('http://purl.org/stuff/rev#hasReview'),$about_rev);
			$model->add($statement); 	
		}//end foreach
		$model_rev=rdfrev($array_rev,$interhash);
		$model->addModel($model_rev);
		$hasReviews = true;
	} else
		$hasReviews = false;
	  
	$header=rdf_header($interhash, $hasTags, $hasUsers, $hasReviews);	  
	$header->addModel($model);
	$header->saveAs("o_".$interhash.".xml");
	filterrdf($datatype,$socialtype,$interhash,$socialdatatype);
	if ($datatype=='both'){
		header('Content-Type: text/xml; charset=ISO-8859-1');
		$data=file_get_contents('o_'.$interhash.'.xml');
		echo $data;
		unlink('o_'.$interhash.'.xml');
	}
} // fine rdf7


function cleanrdf($interhash){
	$local='o_'.$interhash.'.xml';
	$contents = "";
	$fp=@fopen($local,'rb');
	while (!feof($fp)) {
		$contents .=fread($fp, 4096);
	} 
	fclose($fp);
	$contents=str_replace('<![CDATA[','',$contents);
	$contents=str_replace(']]>','',$contents);

	$fp=@fopen($local,'w');

	fwrite($fp,$contents);
	fclose($fp);

	if (file_exists('outputRDF.xml')){
		$local='outputRDF.xml';
		$contents = "";
		$fp=@fopen($local,'rb');
		while (!feof($fp)) {
			$contents .=fread($fp, 4096);
		} 
		fclose($fp);
		$contents=str_replace('<![CDATA[','',$contents);
		$contents=str_replace(']]>','',$contents);

		$fp=@fopen($local,'w');

		fwrite($fp,$contents);
		fclose($fp);
	}
}//fine

function filterrdf($datatype,$socialtype,$interhash,$socialdatatype){
	define("RDFAPI_INCLUDE_DIR","C:/rdfapi-php/api/");
	include(RDFAPI_INCLUDE_DIR . "RdfAPI.php"); 
	$local='o_'.$interhash.'.xml';	
	if ($socialdatatype!=null){
		$uri=HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/".$datatype."/".$socialtype."/".$socialdatatype;// $_SERVER['REQUEST_URI'];
		$model = ModelFactory::getDefaultModel();
		$model->load($local); 
		$r=new Resource($uri);
		$res = $model->find($r, NULL, NULL);
		$res->addNamespace('coll','http://collab.di.uniba.it/colibrary/vocab/');
		$res->addNamespace('tag','http://www.holygoat.co.uk/owl/redwood/0.1/tags/');
		$res->addNamespace('foaf','http://xmlns.com/foaf/0.1/');
		$res->addNamespace('rev','http://purl.org/stuff/rev#');
		$res->addNamespace('moat','http://moat-project.org/ns#');
		$res->saveAs('outputRDF.xml');
		header('Content-Type: text/xml; charset=ISO-8859-1');
		cleanrdf($interhash);
		$data=file_get_contents('outputRDF.xml');
		echo $data; 
	} 
	else {
		if ($datatype=='biblio'){
			$model = ModelFactory::getDefaultModel();
			$model->load($local);  
			$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/biblio");
			$res = $model->find($r, NULL, NULL);
			$res->addNamespace('coll','http://collab.di.uniba.it/colibrary/vocab/');
			$res->addNamespace('tag','http://www.holygoat.co.uk/owl/redwood/0.1/tags/');
			$res->addNamespace('foaf','http://xmlns.com/foaf/0.1/');
			$res->addNamespace('rev','http://purl.org/stuff/rev#');
			$res->addNamespace('moat','http://moat-project.org/ns#');
			$res->saveAs('outputRDF.xml');
			header('Content-Type: text/xml; charset=ISO-8859-1');
			cleanrdf($interhash);
			$data=file_get_contents('outputRDF.xml');
			echo $data;			
		} //fine data=biblio
		
		if ($datatype=='social') {
			$model = ModelFactory::getDefaultModel();
			$model->load($local);
			$model_o=new MemModel();

			$model_o->addNamespace('coll','http://collab.di.uniba.it/colibrary/vocab/');
			$model_o->addNamespace('tag','http://www.holygoat.co.uk/owl/redwood/0.1/tags/');
			$model_o->addNamespace('foaf','http://xmlns.com/foaf/0.1/');
			$model_o->addNamespace('rev','http://purl.org/stuff/rev#');
			$model_o->addNamespace('moat','http://moat-project.org/ns#');

			// $r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social");
			// $res1 = $model->find($r, NULL, NULL);
			// $model_o->addModel($res1);

			if ($socialtype=='all') {
				$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social");
				$res1 = $model->find($r, NULL, NULL);
				$model_o->addModel($res1);
				$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags");
				$res2 = $model->find($r, NULL, NULL);
				$model_o->addModel($res2);

				$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users");
				$res3 = $model->find($r, NULL, NULL);
				$model_o->addModel($res3);

				$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews");
				$res4 = $model->find($r, NULL, NULL);
				$model_o->addModel($res4);


				$obj=array();
				$it = $model_o->getStatementIterator();

				while ($it->hasNext()) {
					$statement = $it->next();
					if ( strpos($statement->getLabelObject(),HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews/")!==false ||
						strpos($statement->getLabelObject(),HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/")!==false ||
						strpos($statement->getLabelObject(),HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/")!==false)
					array_push ($obj,$statement->getLabelObject());
				} //fine while

				foreach ($obj as $o){
					$r=new Resource($o);
					$res=$model->find($r, NULL, NULL);
					$model_o->addModel($res);
					$model_o->saveAs('outputRDF.xml');
				}// enc foreach

				header('Content-Type: text/xml; charset=ISO-8859-1');
				cleanrdf($interhash);
				$data=file_get_contents('outputRDF.xml');
				echo $data;
			} //fine socialtype=all

			if ($socialtype=='tags') {
				$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags");
				$res2 = $model->find($r, NULL, NULL);
				$model_o->addModel($res2);

				$obj=array();
				$it = $model_o->getStatementIterator();

				while ($it->hasNext()) {
					$statement = $it->next();
					if (strpos($statement->getLabelObject(),HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/tags/")!==false)
						array_push ($obj,$statement->getLabelObject());
				} //fine while

				foreach ($obj as $o){
					$r=new Resource($o);
					$res=$model->find($r, NULL, NULL);
					$model_o->addModel($res);
					$model_o->saveAs('outputRDF.xml');
				}// enc foreach
				header('Content-Type: text/xml; charset=ISO-8859-1');
				cleanrdf($interhash);
				$data=file_get_contents('outputRDF.xml');
				echo $data;
			} //fine socialtype=tags


			if ($socialtype=='users') {
				$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users");
				$res2 = $model->find($r, NULL, NULL);
				$model_o->addModel($res2);

				$obj=array();
				$it = $model_o->getStatementIterator();

				while ($it->hasNext()) {
					$statement = $it->next();
					if (strpos($statement->getLabelObject(),HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/users/")!==false)
						array_push ($obj,$statement->getLabelObject());
				} //fine while

				foreach ($obj as $o){
					$r=new Resource($o);
					$res=$model->find($r, NULL, NULL);
					$model_o->addModel($res);
					$model_o->saveAs('outputRDF.xml');
				}// enc foreach
				header('Content-Type: text/xml; charset=ISO-8859-1');
				cleanrdf($interhash);
				$data=file_get_contents('outputRDF.xml');
				echo $data;
			} //fine socialtype=users


			if ($socialtype=='reviews') {
				$r=new Resource(HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews");
				$res2 = $model->find($r, NULL, NULL);
				$model_o->addModel($res2);

				$obj=array();
				$it = $model_o->getStatementIterator();

				while ($it->hasNext()) {
					$statement = $it->next();
					if (strpos($statement->getLabelObject(),HOST.PORTA."/Colibrary/publications/mashup/".$interhash."/social/reviews/")!==false)
						array_push ($obj,$statement->getLabelObject());
				} //fine while

				foreach ($obj as $o){
					$r=new Resource($o);
					$res=$model->find($r, NULL, NULL);
					$model_o->addModel($res);
				}// enc foreach
				$model_o->saveAs('outputRDF.xml');
				header('Content-Type: text/xml; charset=ISO-8859-1');
				cleanrdf($interhash);
				$data=file_get_contents('outputRDF.xml');
				echo $data;
			} //fine socialtype=reviews
		}
	} // fine datatype=social
	cleanrdf($interhash);

}//fine


?>