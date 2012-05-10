<style>
	body, html, table, tr, td {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;	
	}
	input {
		border: solid 1px #000000;
	}
</style>
<table width="100%" height="100%">
  <tr>
    <td>
    <form action="riconosci.php">
    <table align="center" cellspacing="5">
      <tr>
        <td>Password</td>
      </tr>
      <tr>
        <td><input name="password" type="password" id="password" size="30" />
          <input type="submit" name="button" id="button" value="Entra" /></td>
      </tr>
    </table>
   </form>   </td>
  </tr>
  <tr>
    <td align="center">&nbsp;<?php if ($_GET["errore"]) echo "Wrong Password! Access Denied!"; ?>&nbsp;</td>
  </tr>
</table>
