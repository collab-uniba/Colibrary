<?php
	
	session_start();
	if (!$_SESSION["password"]) header("Location: index.php");
	
	@$xml = simplexml_load_file("conf.xml");
	$password = $xml->password;
	$delaySuggest = $xml->delaySuggest;
	$delayMessage = $xml->delayMessage;
?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript">
	$("document").ready(){
		$("#submit").click(function(){
			$.ajax({
				type: "GET",
				url: "modifica.php",
				data: "password=" + password.value + "&delayMessage=" + delayMessage.value + "delaySuggest=" + delaySuggest.value,
				success: function(msg){
					if (msg) alert("Modiche effettuate con successo!");
				}
			});
		});
	});
</script>
<style type="text/css">
<!--

.red {
	color: #FF0000;
}

body {
	top: 50%;
	left: 50%;
	background-color: #666666;
}

td {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}

input, textarea {
	border: 1px solid #000000;
}

#div {
	position:absolute;
	width:485px;
	height:230px;
	z-index:1;
	border: #000000 1px solid;
	top: 50%;
	left: 50%;
	margin-left: -200px;
	margin-top: -144px;
	background-color: #FFFFFF;
}

a {
	text-decoration: none;
	color: #333333;
	font-weight: bold;

}

a:hover {
	text-decoration: none;
	color: #0000FF;	
}
-->
</style>
<div id="div">
<form action="modifica.php" method="get">
  <table width="100%" cellpadding="5" cellspacing="5">
    <tr>
      <td width="53%" align="right">Delay before Suggest Appears (ms):</td>
      <td width="47%"><input type="text" name="delaySuggest" id="delaySuggest" value="<?php echo $delaySuggest; ?>"/></td>
    </tr>
    <tr>
      <td align="right">Delay displaying Messages (ms):</td>
      <td><input type="text" name="delayMessage" id="delayMessage" value="<?php echo $delayMessage; ?>" /></td>
    </tr>
    <tr>
      <td align="right">Password:</td>
      <td><input type="password" value="<?php echo $password; ?>" name="password" id="password" /></td>
    </tr>
    <tr>
      <td height="14" colspan="2" align="center"><?php 
			if ($_GET["fatto"] == 1) echo "<font class='red'>Parameters successfully edited!</font>"; 
			if ($_GET["fatto"] == 2) echo "<font class='red'>RDF Files succesfully deleted!</font>"; 			
		?></td>
    </tr>
    <tr>
      <td height="33" colspan="2" align="center"><input type="submit" name="submit" id="submit" value="Modifica" /></td>
    </tr>
    <tr>
      <td align="left"><a href="deleterdf.php">Delete all RDF files</a><a href="logout.php"></a></td>
      <td align="right"><a href="logout.php">Logout!</a></td>
    </tr>
  </table>
</form>
</div>
