<script type="text/javascript" src="lib/jquery.tooltip.js"></script>
<link rel="stylesheet" href="css/jquery.tooltip.css" type="text/css" >

<form name="form2">
  
	<table width="80%">
		<tr><td>&nbsp;</td></tr>

		<tr>
			<td colspan="2">
				<select name="sel2" id="sel2">
					<option SELECTED value="">&nbsp;</option>
					<option value="title">Title</option>
					<option value="author">Author</option>
				</select>
		  
				<input onkeypress="return disableEnterKey(event)" name="testo2" id="testo2" size="82" autocomplete="off" />
				<input type="hidden" name="delaySuggest2" id="delaySuggest2" />
				<input type="hidden" name="delayMessage2" id="delayMessage2" />
			</td>
			<!--<td align="left">
				<span id="formInfo2_1" title="<div>seleziona il tipo di ricerca bibliografica</div>">
					<img src="img/info.gif" border="0" />
				</span>
			</td>-->
		  
			<td colspan="2" align="right">
				<input type="button" name="submit2" id="submit2" value="Search Publication" class="thickbox" />
				<input type="reset" name="reset2" id="reset2" value="Reset">
			</td>
		</tr>
		
		<tr>
			<td colspan="2"><input type="text" id="isbn2" name="isbn2" size="16" style="visibility:hidden" /></td>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
    
	</table>
</form>

<script type="text/javascript">

$(document).ready(function() { 
	
$('#formInfo2_1').tooltip();				
});
</script>
