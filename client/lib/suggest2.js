(function($) {

	var offSet = $("#testo").offset(); // prende la posizione del text Testo

	/********************** disabilito tutti i text *******************/
	$("#testo").attr("value", "").attr("disabled","disabled");
	$("#data").attr("value", "").attr("disabled","disabled");
	$("#SocialType").attr("value", "").attr("disabled","disabled");
	$("#TagSource").attr("value", "").attr("disabled","disabled");
	$("#ReviewSource").attr("value", "").attr("disabled","disabled");
	$("#tag_limit").attr("value", "").attr("disabled","disabled");
	/********************** disabilito tutti i text *******************/	
	
	


})(jQuery);