<?php
function cleanString($string)  
{  
// Replace other special chars  
$specialCharacters = array(  
'è' => '�',  
'ò' => '�',  
'ì' => '�',  
'ù' => '�',
'�' => '�',
'"' => '\'',
); 
  
while (list($character, $replacement) = each($specialCharacters)) {  
 $string = str_replace($character, $replacement, $string);
 }
  
return $string;  
}


?>