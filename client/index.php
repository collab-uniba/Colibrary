<html>
<head>
  
  
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <link rel="stylesheet" href="http://view.jquery.com/tags/ui/latest/themes/flora/flora.all.css" type="text/css" media="screen" title="Flora (Default)">  

  <script type="text/javascript" src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.core.js"></script>
  <script type="text/javascript" src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.accordion.js"></script>
  <script type="text/javascript" src="http://view.jquery.com/tags/ui/latest/ui/jquery.ui.all.js"></script>
  
  <link href="css/suggest.css" rel="stylesheet" type="text/css">
  <link href="css/body.css" rel="stylesheet" type="text/css">
  <link href="css/jTip.css" rel="stylesheet" type="text/css">
  <script src="lib/xml2json.js"></script>
  <script src="lib/suggest.js"></script>
  <script src="lib/suggestpubs.js"></script>


  <script type="text/javascript">
  $(document).ready(function(){
    $("#colibrary > ul").tabs();			
  });

  function disableEnterKey(e)
  {
	 var key;     
	 if(window.event) key = window.event.keyCode; //IE
	 else key = e.which; //firefox     

	 return (key != 13);
  }
  

  </script>
  
</head>
<body OnKeyPress="return disableKeyPress(event)" background="img/sfondo.png" style="background-repeat: repeat-X; font-size:17px; font-family:Arial; font-weight:bold">
<center><h1 class="Stile1"><font size="15">COLIBRARY</font></h1></center>
<div id="colibrary" class="flora">
        <ul>

            <li><a href="#fragment-1"><span>Books</span></a></li>
            <li><a href="#fragment-2"><span>Scientific Publications</span></a></li>
        </ul>
        <div id="fragment-1" ><?php include("books_menu.php"); ?></div>
        <div id="fragment-2"><?php include("pubs_menu.php"); ?></div>
    </div>
    <br />
	<div style="border: 1px #999999 solid; padding: 3px;">
		Colibrary Project - <a href="http://collab.di.uniba.it">Laboratorio di Ricerca per la Collaborazione in Rete</a> - Dipartimento di Informatica - Universita' di Bari
	</div>
</body>
</html>
