 
<form name="form1_2" >
  <table width="50%">
    
    <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
    <tr >
		<td>
			<select size=1 name="SocialType" id="SocialType">
				<!-- <option selected> -->
				<option  SELECTED value=""> </option>
				<option   value="both"> All</option>
				<option value="tags"> Only Tags</option>
				<option value="reviews"> Only Reviews</option>
			</select>
		</td>
    
<!-- eliminato help -->
		 
 		<td><strong>Review Source</strong></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<SELECT size=1 NAME="ReviewSource" id="ReviewSource">
				<OPTION  selected value=""></option>
				<OPTION  selected value="both">All</option>
				<OPTION value="amazon">Amazon</option>
				<option value="anobii">Anobii</option>
				<option value="librarything">Library Thing</option>
            </select>
	    </td>
	</tr>
			 

		
<!--		
	  <td>&nbsp</td>
      
	  
	  <td>
	  
	    <table>
	     <tr><td><strong>Review Source</strong></td></tr>		
		 <tr><td>&nbsp;</td></tr>

	     <tr><td><SELECT size=1 cols=4 NAME="ReviewSource" id="ReviewSource">
              <!-- <OPTION selected> -->
<!--
              <OPTION  selected value=""></option>
              <OPTION  selected value="both">All</option>
              <OPTION value="amazon">Amazon</option>
              <option value="anobii">Anobii</option>
              <option value="librarything">Library Thing</option>
             </select>
	     </td>
			 
		</tr>
	    <tr><td colspan="2">&nbsp;</td></tr>
	    <tr><td colspan="2">&nbsp;</td></tr>
-->
<!--
		<tr><td><strong>Tag Source</strong></td></tr>  
		<tr><td>&nbsp;</td></tr>
   	
	    <tr><td><SELECT size=1 cols=4 NAME="TagSource" id="TagSource">
               <!-- <OPTION selected> -->
<!--
              <OPTION  selected value="" > </option>
              <OPTION   value="both">All</option>
              <OPTION value="librarything">Library Thing</option>
              <OPTION value="anobii">Anobii</option>
              </select>
	         </td>
        </tr>
	
	    <tr><td colspan="2">&nbsp;</td></tr>
		<tr>
          <td colspan="2">Extract only the first&nbsp;
          <select size=1 cols=4 name="tag_limit" id="tag_limit" value="1">
            <option selected value=10>10</option>
            <option value=20>20</option>
            <option value=50>50</option>
          </select>&nbsp;tags 
	      </td>
        </tr>
		</table>
		 
	  </td>
	</tr> 
 -->       
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
   
  </table>
	
</form>

<script type="text/javascript">

$(document).ready(function() { 
	
$('#formInfo2').tooltip();				
});
</script>