$("document").ready(function(){
		
	var suggest = "#suggest";
	var itemDetail = "#itemDetail";
	var testo = "#testo";
	var offSet = $(testo).offset();

	$("body").append("<div id='message'></div>"); // div messaggi vari
	$("body").append("<div id='suggest'></div>"); // aggiungo il DIV che mostrerà gli ISBN trovati
	$("#message").hide();
	
	$("body").append("<div id='itemDetail'></div>"); // aggiungo il DIV che mostrerà i dettagli dei libri
	$(itemDetail).hide();


	$(testo).attr("value", "").attr("disabled","disabled");
	$("#TagSource").attr("value", "").attr("disabled","disabled");
	$("#ReviewSource").attr("value", "").attr("disabled","disabled");
	$("#tag_limit").attr("value", "").attr("disabled","disabled");	
    $("#SocialType").attr("value", "").attr("disabled","disabled");			
	//disabilito il submit fino a qando nn si è sceltto un libro
	$("#submit").attr("disabled","disabled");			
	
	$("#submit").click(function(){
		$("#isbn").val($("#testo").val());		
		if (!$("#testo").val() && !$("#isbn").val()) {
			$("body").prepend("<div id='loading'></div>");			
			$("#message").html("<br /><br />Please fill-in book information!").show("normal");
			setTimeout(function(){
				$("#message").hide("normal");
				$("#loading").hide();
			}, 3000);
			return false;	
		}
		else { // simulo una form con invio get mediante un location.href...i dati vengono spediti a book_res.php
		    var url='book_res.php?isbn='+$("#testo").val();			
			var porta='';	
			var local_path='http://'+window.location.hostname+porta+'/ColibraryClient/';
			if ($("#SocialType").val()==''){
			url=url+'&data=biblio';
			 }
			if ($("#SocialType").val()=='tags'){
			 if(!$("#TagSource").val()) tag_source='both'; else tag_source=$("#TagSource").val();
			 if(!$("#tag_limit").val()) tag_limit=20; else tag_limit=$("#tag_limit").val();
			url=url+'&data=both&SocialType=tags&TagSource='+tag_source+'&tag_limit='+tag_limit;
			 }
			 if ($("#SocialType").val()=='reviews'){
			  if(!$("#ReviewSource").val()) rev_source='both'; else rev_source=$("#ReviewSource").val();
			  //if(!$("#review_limit").val()) review_limit=10; else review_limit=$("#review_limit").val();
			url=url+'&data=both&SocialType=reviews&ReviewSource='+rev_source;
			 }
			 if ($("#SocialType").val()=='both'){
			  if(!$("#TagSource").val()) tag_source='both'; else tag_source=$("#TagSource").val();
			  if(!$("#tag_limit").val()) tag_limit=20; else tag_limit=$("#tag_limit").val();
			  if(!$("#ReviewSource").val()) rev_source='both'; else rev_source=$("#ReviewSource").val();
              //if(!$("#review_limit").val()) review_limit=10; else review_limit=$("#review_limit").val();			  
			 url=url+'&data=both&SocialType=both&ReviewSource='+rev_source+'&TagSource='+tag_source+'&tag_limit='+tag_limit;
			 }
			$("body").prepend("<div id='loading'></div>");
			$("#message").html("<br /><br /><img src='img/ajax-loader.gif' />&nbsp;Loading, please wait...").show("normal");	
            //alert(local_path+url);
			location.href=local_path+url;			
		}
	});
	
	$("#sel1").change(function(){
		if ($("#sel1").val() == "ISBN") $('#submit').removeAttr('disabled');
    	if ($("#sel1").val() == "") $(testo).attr("value", "").attr("disabled","disabled");
		else {
			$(testo).removeAttr("disabled");
			$(testo).val("");
			$(itemDetail).hide();
			$(suggest).hide();
			//$("#SocialType").attr("value", "").attr("disabled","disabled");
			$("#TagSource").attr("value", "").attr("disabled","disabled");
			$("#ReviewSource").attr("value", "").attr("disabled","disabled");
			$("#tag_limit").attr("value", "").attr("disabled","disabled");			
		}
	});
	
	
	$(testo).change(function(){
     $("#SocialType").removeAttr("disabled");
	
	});
	
	/*var top='225px';			
	if (navigator.userAgent.indexOf('Firefox')!=-1) top='235px' ;			
	*/
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

			if ($("#sel1").val() == "ISBN") {
			$("#data").removeAttr("disabled");
			
			}
			
			//abilita i campi del form, a seconda dlle scelte degli input type precedenti
			
			
			$("#SocialType").change(function(){
				if ($("#SocialType").val() == "tags") {
					$("#TagSource").removeAttr("disabled");
					$("#ReviewSource").attr("value", "").attr("disabled","disabled");
					$("#tag_limit").removeAttr("disabled");
				}
				if ($("#SocialType").val() == "reviews") {
					$("#TagSource").attr("value", "").attr("disabled","disabled");
					$("#ReviewSource").removeAttr("disabled");
					$("#tag_limit").attr("value", "").attr("disabled","disabled");
				}
				if ($("#SocialType").val() == "both") {
					$("#TagSource").removeAttr("disabled");
					$("#ReviewSource").removeAttr("disabled");
					$("#tag_limit").attr("value", "").removeAttr("disabled");
				}											
			});


			if ($("#sel1").val() != "ISBN") { // se la ricerca è per ISBN allora non visualizzare il box Suggest
			
				var loadingtext = "Searching ISBNs...";
				var loading = "<p align='center'><img src='img/ajax-loader.gif'> " + loadingtext + "</p>";
				$(suggest).html(loading).show();


				setTimeout(function(){
					if (lunghezza == document.getElementById("testo").value.length) {
						switch(e.keyCode) {
							case 27:
								$(suggest).hide();
								$(itemDetail).hide();
							break;

							default:							

								var url_isbn = "";
								switch($("#sel1").val()) {
									case "Author":
										url_isbn = "getISBN.php";
									break;
									case "OTitle":
										url_isbn = "getISBNTit.php";
									break;
									case "MTitle":
										url_isbn = "getISBNTitMore.php";
									break;								
								}
	
								req = $.ajax({
									type: "GET",
									dataType: "XML",
									url: url_isbn,
									data: "testo=" + $(testo).val(),
									success: function(xml){
										$(suggest).empty();
										var json = $.xml2json(xml);
										var isbn_flag = false;										
										$.each(json.book, function(i, item){
											isbn = item.isbn;
											if (isbn) {
												$(suggest).append("<li class='isbn'><IMG SRC='img/books.jpeg' width='20px' height='20px'>"+ isbn + "</li>");
												isbn_flag = true;												
											}
										});
																														
										if (isbn_flag == false) {
											$(suggest).html("No ISBN found");
										}
																				
										$(".isbn").bind("mouseenter keyup", function(e){
											//if ($(this).text() != currentisbn) reqimage.abort();
											var currentisbn = $(this).text();
											$(this).css({ "background-color" : "#66FFCC" });
											$(itemDetail).css({ 
												"padding" : "10",
												"position" : "absolute",
												"left" : '780px',
												"top" : '205px',
     											//"zIndex": 10,
												"width":'350px',
												"border": "1px solid black"
											}).show();
												
											var loadingtext = "Retrieving Book Information...";
											var loading = "<p>&nbsp;</p><p>&nbsp;</p>&nbsp;<p></p><p align='center'><img src='img/ajax-loader.gif'> "+loadingtext+"</p>";
											$(itemDetail).html(loading);
											$(itemDetail).empty();
											$.each(json.book, function(i, item) {
												if (item.isbn == currentisbn) {
													if (!item.title && !item.author && !item.manufacturer) $(itemDetail).append("<p align='center'><img src='img/divieto.jpg' /></p><p align='center'>No information available!</p>");
													else {
														$(itemDetail).html("<img id='imgbook' align='left' src='img/loadingAnimation.gif' width='150' border='0' />");														
														reqimage = $.ajax({
															type: "GET",
															url: "getImage.php",
															dataType: "script",
															data: "url=" + item.url,
															success: function(img) {
																if (!img) { $("#imgbook").attr("src", "img/noimg.jpg"); }
																else { $("#imgbook").attr("src", img); }
																reqimage = null;																
															}
														});
														
														
														$(itemDetail).append("<p><b style='color:red'>Titolo</b><br />" + item.title + "</p>");
														$(itemDetail).append("<p><b style='color:red'>Autore</b><br />" + item.author + "</b></p>");
														$(itemDetail).append("<p><b style='color:red'>Casa Editrice</b><br />" + item.manufacturer + "</b></p>");
														$(itemDetail).append("</p>");
													}
												}
											});
										});
		
										$(".isbn").bind("mouseleave", function(){
											$(this).css({ "background-color" : "#FFFFFF" });
											$(itemDetail).hide();
										});
										
										$(".isbn").click(function(){
											$(testo).val($(this).text());
											$("#isbn").val($(this).text());
											$(suggest).hide();
											$("#data").removeAttr("disabled");
											//riabilito il submit
											$('#submit').removeAttr("disabled");
	
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
