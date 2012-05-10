 
<form name="form2_2" >
  <table width="50%">
    
    <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
    
    <tr>
		<td rowspan="3">
			<select size=1 cols=4 name="SocialType2" id="SocialType2">
				<!-- <option selected> -->
				<option SELECTED value=""></option>
				<option value="both"> All</option>
				<option value="tags"> Only Tags</option>
				<option value="reviews"> Only Reviews</option>
				<option value="users"> Only Users</option>
			</select>
		</td>
    
	    <!--<td>
			<span id="formInfo2_2" title="<div>dopo aver selezionato il tipo di ricerca bibliografica<br>seleziona il tipo di informazioni sociali,<br>relative a tags, reviews e users che desideri ricevere</div>">
                <img src="img/info.gif" border="0" /></a>
			</span>
		</td> 
		<td>&nbsp;</td>-->
      
	  
		<td>	  
			<table>
				<tr><td><strong>Review Source</strong></td></tr>		
				<tr><td>&nbsp;</td></tr>

				<tr>
					<td>
						<SELECT size=1 cols=4 NAME="ReviewSource2" id="ReviewSource2">
							<!-- <OPTION selected> -->
							<option SELECTED value="">&nbsp;</option>
							<OPTION  value="all">All</option>
							<OPTION value="bibsonomy"> Bibsonomy</option>
							<OPTION value="citeulike"> CiteULike</option>
							<OPTION value="acm"> ACM</option>
						</select>
					</td>
				 
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
			
				<tr><td><strong>User Source</strong></td></tr>  
				<tr><td>&nbsp;</td></tr>
			
				<tr>
					<td>
						<SELECT size=1 cols=4 NAME="UserSource2" id="UserSource2">
							<!-- <OPTION selected> -->
							<option SELECTED value="">&nbsp;</option>
							<OPTION  value="all">All</option>
							<OPTION value="bibsonomy"> Bibsonomy</option>
							<OPTION value="citeulike"> CiteULike</option>
							<OPTION value="acm"> ACM</option>
						</select>
					</td>					
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>

				<tr><td><strong>Tag Source</strong></td></tr>  
				<tr><td>&nbsp;</td></tr>
		
				<tr>
					<td>
						<SELECT size=1 cols=4 NAME="TagSource2" id="TagSource2">
							<!-- <OPTION selected> -->
							<option SELECTED value="">&nbsp;</option>
							<OPTION  value="all">All</option>
							<OPTION value="bibsonomy"> Bibsonomy</option>
							<OPTION value="citeulike"> CiteULike</option>
							<OPTION value="acm"> ACM</option>
						</select>
					</td>
				</tr>
		
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td colspan="2">Extract only the first&nbsp;
						<select size=1 cols=4 name="tag_limit2" id="tag_limit2" value="1">
						<option selected value=10>10</option>
						<option value=20>20</option>
						<option value=50>50</option>
						</select>&nbsp;tags 
					</td>
				</tr>
			</table>		 
		</td>
	</tr> 
        
    <!--<tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>-->
   
  </table>
</form>

<script type="text/javascript">

$(document).ready(function() { 
	
$('#formInfo2_2').tooltip();				
});
</script>