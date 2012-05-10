<script type="text/javascript" src="lib/jquery.tooltip.js"></script>
<link rel="stylesheet" href="css/jquery.tooltip.css" type="text/css" >


<form name="form1" >
	<table width="80%">
		<tr><td>&nbsp;</td></tr>

		<tr>
			<td colspan="2">
				<select name="sel1" id="sel1">
					<option SELECTED value="">&nbsp;</option>
					<option value="Author">Author</option>
					<option value="MTitle">Keywords in the Title</option>
					<!--<option value="OTitle">String (most likely) contained in the Title</option>-->
					<option value="ISBN">ISBN</option>
				</select>
				<input onkeypress="return disableEnterKey(event)" name="testo" id="testo" size="40" autocomplete="off" />
				<input type="hidden" name="delaySuggest" id="delaySuggest" />
				<input type="hidden" name="delayMessage" id="delayMessage" />
			</td>
			<td  align="left">
<!--
			<span id="formInfo" title="<div>Please select the type of search you want to perform:<br/>by Author<br/>by Keywords<br/>by String<br/>by ISBN<br/></div>">
				<img src="img/info.gif" border="0" /></a>
			</span>-->
			</td>
			<td colspan="2" align="right">
				<input type="button"  name="submit" id="submit" value="Search Book" class="thickbox" >
				<input type="reset" name="reset" id="reset" value="Reset">
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="text" id="isbn" name="isbn" size="16" style="visibility:hidden" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
    
	</table>
</form>

<script type="text/javascript">

$(document).ready(function() { 
	
$('#formInfo').tooltip();				
});
</script>
