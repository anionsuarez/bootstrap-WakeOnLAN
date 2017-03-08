<?php
error_reporting(E_ALL);
$timer_start=microtime(); // devuelve la fecha del sistema

/**************** configuracion de la DB **********/
$host="localhost";
$username="wol";
$password="wol.713!";
$database="wol";
/**************************************************/

function DBConnect() { 
  global $host,$username,$password,$database;
  $conn = new PDO("mysql:host=$host;dbname=$database",$username,$password);
  return $conn;
}

// $arrowDown='&#8595;'; // 0x2191 unicode  -------- BORRAR

function back($mesg="&nbsp;Spä&nbsp;") {
  echo "<center><FORM ACTION=\"./\" METHOD=\"get\">";
  echo "<INPUT TYPE='hidden' NAME='uniq' VALUE='".time()."'>";
  echo "<INPUT TYPE='submit' NAME='btnr' VALUE='$mesg'>";
  echo "</FORM></center>\n";
}

function countx($array) { // count non-empty elements
 $c=0;
 foreach($array as $element) if ($element) $c++;
 return $c;
}

function myerror($txt, $die=true) {
 echo "\n<div class='myerror'><b>ERROR:</b> $txt</div>\n";
 if ($die) {
  // wolFooter();
   die("<!-- myerror() -->");
 }
}


?>