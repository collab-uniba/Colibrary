<?php
function cleanString($string)  
{  
// Replace other special chars  
$specialCharacters = array(  
'Ã¨' => 'è',  
'Ã²' => 'ò',  
'Ã¬' => 'ì',  
'Ã¹' => 'ù',
'Ã' => 'à',
'"' => '\'',
); 
  
while (list($character, $replacement) = each($specialCharacters)) {  
 $string = str_replace($character, $replacement, $string);
 }
  
return $string;  
}


?>