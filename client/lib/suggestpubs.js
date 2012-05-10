$("document").ready(function(){
		
	var suggest = "#suggest2";
	var itemDetail = "#itemDetail2";
	var testo = "#testo2";
	var offSet = $(testo).offset();
    var text_title="";
    var text_author="";
	var interhash='';

	$("body").append("<div id='message2'></div>"); // div messaggi vari
	$("body").append("<div id='suggest2'></div>"); // aggiungo il DIV che mostrerà gli ISBN trovati
	$("#message2").hide();
	
	$("body").append("<div id='itemDetail2'></div>"); // aggiungo il DIV che mostrerà gli ISBN trovati
	$(itemDetail).hide();

	//disabilito il submit fino a qando nn si è sceltto una pub
	$("#submit2").attr("disabled","disabled");			


	$(testo).attr("value", "").attr("disabled","disabled");
	
	$("#TagSource2").attr("value", "").attr("disabled","disabled");
	$("#tag_limit2").attr("value", "").attr("disabled","disabled");	
	$("#UserSource2").attr("value", "").attr("disabled","disabled");	
	$("#ReviewSource2").attr("value", "").attr("disabled","disabled");	
    $("#SocialType2").attr("value", "").attr("disabled","disabled");			

	
	
	$("#sel2").change(function(){
		if ($("#sel2").val() == "") $(testo).attr("value", "").attr("disabled","disabled");
		else {
			$(testo).removeAttr("disabled");
			$(testo).val("");
			$(itemDetail).hide();
			$(suggest).hide();
			$("#TagSource2").attr("value", "").attr("disabled","disabled");
			$("#tag_limit2").attr("value", "").attr("disabled","disabled");			
			$("#UserSource2").attr("value", "").attr("disabled","disabled");			
			$("#ReviewSource2").attr("value", "").attr("disabled","disabled");			
			}
	});
	
	$(testo).change(function(){
     $("#SocialType2").removeAttr("disabled");
	
	});
	
					
	$(suggest).css({ // gli creo uno stile
		position: "absolute",
		backgroundColor: "#ffffff",
		top: '235px',
		left: '400px',
		width: '363px',
       // zIndex: 0,
		border: "1px solid #666666"
	});
			
	$(suggest).hide(); // lo nascondo. Lo mostrerò quando dovrà visualizzare i risultati					
	$(testo).bind("keyup click", function(e){ // quando viene digitato un testo all'interno invio la ricerca
		var lunghezza = this.value.length;
				
		if (this.value.length >= 3) {

     		$(suggest).hide();
			$(itemDetail).hide();
			//abilita i form, a seconda dlle scelte dei form precedenti

			if ($("#sel2").val() == "doi_isbn") $("#data2").removeAttr("disabled");
			
			$("#data2").change(function(){
				if ($("#data2").val() == "both" || $("#data2").val() == "social") {
											
						$("#SocialType2").removeAttr("disabled");

				}
				else {
					$("#UserSource2").attr("value", "").attr("disabled","disabled");
					$("#SocialType2").attr("value", "").attr("disabled","disabled");
					$("#TagSource2").attr("value", "").attr("disabled","disabled");
					$("#tag_limit2").attr("value", "").attr("disabled","disabled");											
				}
			});
			
            $("#SocialType2").change(function(){
				if ($("#SocialType2").val() == "both"){
											
						$("#TagSource2").removeAttr("disabled");
						$("#ReviewSource2").removeAttr("disabled");
						$("#UserSource2").removeAttr("disabled");	
						$("#tag_limit2").attr("value", "").removeAttr("disabled");		
						
				}
				if ($("#SocialType2").val() == "users"){
											
						$("#UserSource2").removeAttr("disabled");	
						$("#TagSource2").attr("value", "").attr("disabled","disabled");
						$("#ReviewSource2").attr("value", "").attr("disabled","disabled");
					    $("#tag_limit2").attr("value", "").attr("disabled","disabled");	
						
				}
				if ($("#SocialType2").val() == "reviews"){
											
						$("#ReviewSource2").removeAttr("disabled");	
						$("#TagSource2").attr("value", "").attr("disabled","disabled");
						$("#UserSource2").attr("value", "").attr("disabled","disabled");
					    $("#tag_limit2").attr("value", "").attr("disabled","disabled");	
						
				}
				if ($("#SocialType2").val() == "tags"){
											
						$("#TagSource2").removeAttr("disabled");	
						$("#tag_limit2").removeAttr("disabled");	
						$("#UserSource2").attr("value", "").attr("disabled","disabled");
						$("#ReviewSource2").attr("value", "").attr("disabled","disabled");

						
				}
            });
						
			if ($("#sel2").val() != "doi_isbn") { // se la ricerca è per ISBN allora non visualizzare il box Suggest
			
				var loadingtext = "Searching Titles...";
				var loading = "<p align='center'><img src='img/ajax-loader.gif'> " + loadingtext + "</p>";
				$(suggest).html(loading).show();

				
				setTimeout(function(){
				/*alert(lunghezza);
				alert(';'+document.getElementById("testo2").value.length);
				alert(';'+document.getElementById("testo2").value);
				*/
					if (lunghezza == document.getElementById("testo2").value.length) {
						switch(e.keyCode) {
							case 27:
								$(suggest).hide();
								$(itemDetail).hide();
							break;

							default:							

								var url_isbn = "";
								switch($("#sel2").val()) {
									case "author":
										url_isbn = "get_by_author.php";
									break;
									// nn implementato per doi_isbn, in qanto inefficiente
									case "doi_isbn":
										url_isbn = "getDOI.php";
									case "title":
										url_isbn = "get_by_title.php";
									
																	
								}
	
								req = $.ajax({
									type: "GET",
									dataType: "XML",
									url: url_isbn,
									data: "testo=" + $(testo).val(),
									success: function(xml){
										$(suggest).empty();
										var json = $.xml2json(xml);
										if (json.n_of_instances==0) {
											$(suggest).html("No Publications found!");
										}
										
										else if (json.n_of_instances>1){
											var i=0;
											$.each(json.publication, function(i, item){
												tit = item.title;
												if (tit) {
													if (item.entry_type=='inproceedings' || item.entry_type=='misc'){
														if (item.book_title)
												 //numero con i le publications suggerite simulando (e rendendolo invisibile come html) un codice da 0 a 9 per ognuna di esse
												 //  facilitandomi il match cn il tag <n> generato nel file xml output di get_by_title.php e
												 //  get_by_author.php
															$(suggest).append("<li class='isbn'><div style='visibility:hidden'>"+i+"***</div><IMG SRC='img/pubs.jpeg' width='20px' height='20px'>" + item.book_title + "</li>");
														else 	
															$(suggest).append("<li class='isbn'><div style='visibility:hidden'>"+i+"***</div><IMG SRC='img/pubs.jpeg' width='20px' height='20px'>" + item.year + "</li>");
													}
													if (item.entry_type=='article' || item.entry_type=='incollection'){
														$(suggest).append("<li class='isbn'><div style='visibility:hidden'>"+i+"***</div><IMG SRC='img/pubs.jpeg' width='20px' height='20px'>" + item.journal + ' ' + item.year +"</li>");
													}
												 
													if (item.entry_type=='book'){
														$(suggest).append("<li class='isbn'><div style='visibility:hidden'>"+i+"***</div><IMG SRC='img/books2.gif' width='20px' height='20px'>" + item.year +"</li>");
													}
												}
										       
												i++;
											});
										}
										else if(json.publication.doi_isbn!="No Publications found!")
											$(suggest).append("<li class='isbn'>" + json.publication.title + "</li>");
										else		
											$(suggest).html("No Publications found!");		 													
																									
										$(".isbn").bind("mouseenter keyup", function(e){
											//if ($(this).text() != currentisbn) reqimage.abort();
											var current_tit = $(this).text();
											current_tit=current_tit.substr(0,current_tit.indexOf('***'));//alert(current_tit);

											$(this).css({ "background-color" : "#66FFCC" });
											$(itemDetail).css({ 
												"padding" : "10",
												"position" : "absolute",
												"left" : '780px',
												"top" : '235px',
     											//"zIndex": 10,
												"width":'350px',
												"border": "1px solid black"
											}).show();
												
										
										var loadingtext = "Retrieving Publication Information...";
										var loading = "<p>&nbsp;</p><p>&nbsp;</p>&nbsp;<p></p><p align='center'><img src='img/ajax-loader.gif'> "+loadingtext+"</p>";
										$(itemDetail).html(loading);
										$(itemDetail).empty();
											
										if (json.n_of_instances>1){
											$.each(json.publication, function(i, item) {
											
																						  
												if (item.n == current_tit) {
													if (!item.doi_isbn && !item.author && !item.journal && !item.publisher && !item.year) 
													$(itemDetail).append("<p align='center'><img src='img/divieto.jpg' /></p><p align='center'>No information available!</p>");
													else {
														$(itemDetail).append("<p><b style='color:red'>Title</b><br />" + item.title + "</p>");
														$(itemDetail).append("<p><b style='color:red'>Author</b><br />" + item.author + "</b></p>");
														if (item.publisher) $(itemDetail).append("<p><b style='color:red'>Publisher</b><br />" + item.publisher + "</b></p>");
														if (item.journal) $(itemDetail).append("<p><b style='color:red'>Journal</b><br />" + item.journal + "</b></p>");
														$(itemDetail).append("<p><b style='color:red'>Year</b><br />" + item.year + "</b></p>");
														$(itemDetail).append("</p>");
    													interhash=item.interhash;

													}
												 text_title=item.title;
												 text_author=item.author;
												}
											  
											});
										}
										
										else if (json.n_of_instances==1){
							
			         						   if (!json.publication.doi_isbn && !json.publication.author && !json.publication.journal && !json.publication.publisher && !json.publication.year) 
											   $(itemDetail).append("<p align='center'><img src='img/divieto.jpg' /></p><p align='center'>No information available!</p>");
												
											   else {

											  	$(itemDetail).append("<p><b style='color:red'>Title</b><br />" + json.publication.title + "</p>");
											    $(itemDetail).append("<p><b style='color:red'>Author</b><br />" + json.publication.author + "</b></p>");
											    if (item.publisher) $(itemDetail).append("<p><b style='color:red'>Publisher</b><br />" + json.publication.publisher + "</b></p>");
											    if (item.journal) $(itemDetail).append("<p><b style='color:red'>Journal</b><br />" + json.publication.journal + "</b></p>");
											    $(itemDetail).append("<p><b style='color:red'>Year</b><br />" + json.publication.year + "</b></p>");
											    $(itemDetail).append("</p>");
												interhash=json.publication.interhash;
											    }
											}
																					
										});
	


       $("#submit2").click(function(){
		$("#isbn2").val($("#testo2").val());		
		if (!$("#testo2").val() && !$("#isbn2").val()) {
			$("body").prepend("<div id='loading2'></div>");			
			$("#message2").html("<br /><br />Please fill-in publication information!").show("normal");
			setTimeout(function(){
				$("#message2").hide("normal");
				$("#loading2").hide();
			}, 3000);
			return false;	
		}
		else {
		
			 // simulo una form con invio get mediante un location.href...
		    var url='publication_res.php?interhash='+interhash;
			var porta='';
			var local_path='http://'+window.location.hostname+porta+'/ColibraryClient/';
			if ($("#SocialType2").val()==''){
			url=url+'&data2=biblio';
			 }
			if ($("#SocialType2").val()=='tags'){
			 if(!$("#TagSource2").val()) tag_source='both'; else tag_source=$("#TagSource2").val();
			 if(!$("#tag_limit2").val()) tag_limit=20; else tag_limit=$("#tag_limit2").val();
			url=url+'&data2=both&SocialType2=tags&TagSource2='+tag_source+'&tag_limit2='+tag_limit;
			 }
			 if ($("#SocialType2").val()=='reviews'){
			  if(!$("#ReviewSource2").val()) rev_source='both'; else rev_source=$("#ReviewSource2").val();
			  //if(!$("#review_limit").val()) review_limit=10; else review_limit=$("#review_limit").val();
			url=url+'&data2=both&SocialType2=reviews&ReviewSource2='+rev_source;
			 }
			 
			 if ($("#SocialType2").val()=='users'){
			  if(!$("#UserSource2").val()) user_source='both'; else user_source=$("#UserSource2").val();
    			url=url+'&data2=both&SocialType2=users&UserSource2='+user_source;
			 }
			 
			 if ($("#SocialType2").val()=='both'){
			  if(!$("#TagSource2").val()) tag_source='both'; else tag_source=$("#TagSource2").val();
			  if(!$("#tag_limit2").val()) tag_limit=20; else tag_limit=$("#tag_limit2").val();
			  if(!$("#ReviewSource2").val()) rev_source='both'; else rev_source=$("#ReviewSource2").val();
			  if(!$("#UserSource2").val()) user_source='both'; else user_source=$("#UserSource2").val();
    			 url=url+'&data2=both&SocialType2=both&ReviewSource2='+rev_source+'&UserSource2='+user_source+'&TagSource2='+tag_source+'&tag_limit2='+tag_limit;
			 }
			$("body").prepend("<div id='loading2'></div>");
			$("#message").html("<br /><br /><img src='img/ajax-loader.gif' />&nbsp;Loading, please wait...").show("normal");	
            //alert(url);
			location.href=local_path+url;			
		}
	});	
		
										$(".isbn").bind("mouseleave", function(){
											$(this).css({ "background-color" : "#FFFFFF" });
											$(itemDetail).hide();
										});
										
										$(".isbn").click(function(){
											//$(testo).val($(this).text());
											$(testo).val(text_title);
											//$("#isbn2").val($(this).text());
											$("#isbn2").val(text_author);
											$(suggest).hide();
											$("#data2").removeAttr("disabled");
											//riabilito il submit
											$('#submit2').removeAttr("disabled");
	
										});
										
										$().click(function(){
											$(suggest).hide();
										});										
								}	
								}); // chiusura chiamata ajax
							break;
						} // chiusura switch
					} // fine controllo su lunghezza testo
				}, 4000);
			}
		}
		else {
			$(suggest).hide();
			$(itemDetail).hide();
		}
	});
});
