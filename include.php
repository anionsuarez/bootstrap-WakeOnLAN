<?php
error_reporting(E_ALL);
$timer_start=microtime(); // devuelve la fecha del sistema

/**************** configuracion de la DB **********/
$m_host="localhost";
$m_user="wol";
$m_pass="wol.713!";
$m_data="wol";
/**************************************************/

function DBConnect() { // *** pripojenie + nastavenie servera ***
  global $m_host,$m_user,$m_pass,$m_data;
  $conn = mysql_pConnect($m_host,$m_user,$m_pass);
  if (!$conn) error("No se pudo conectar al servidor, revise los datos en include.php",mysql_error());
  if (!mysql_select_db($m_data)) error("No se encontro la base de datos, revise los datos en include.php o cree la base",mysql_error());
  return $conn;
}

$arrowDown='&#8595;'; // 0x2191 unicode

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

function wolHeader($refresh="") { ?>
<HTML>
<HEAD>
 <TITLE>WakeOnLan + ping</TITLE>
 <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1250">
<?php if ($refresh) echo " <META HTTP-EQUIV=\"refresh\" CONTENT=\"$refresh\">\n";?>
 <STYLE>

H1, H2 { text-align: center; }
BODY, TD, TH, FORM, INPUT, SELECT, P { font-family: Verdana; font-size: 10pt; }
.help { text-align: center; font-size: 9pt; }
TH { font-size: 11pt; }

TABLE { border-collapse: collapse; }
TD,TH { border: solid thin black; }
TD.nb { border: none; }

A.sort       { color: black; text-decoration: none; }
A.sort:hover { color: red;   text-decoration: underline; }

 </STYLE>
</HEAD>
<BODY>
<?php }

function wolFooter() {
 global $timer_start;
 echo "\n<hr width=\"50%\"><div style=\"text-align: center; font-size: 8pt;\">";
 echo "&copy; Marki, 2004<br>";
 // vypocet doby trvania skriptu
 global $timer_start;
 $timer_end=microtime();
 ereg('0(\..*) (.*)',$timer_start,$t_s);
 ereg('0(\..*) (.*)',$timer_end  ,$t_e);
 $timer_s_time=$t_s[2].$t_s[1];
 $timer_e_time=$t_e[2].$t_e[1];
 $timer_elapsed=sprintf("%.4f",$timer_e_time-$timer_s_time);
 echo "Página generada en: <b>$timer_elapsed</b> sek.</div>\n";
 echo "\n</BODY></HTML>";
}

function myerror($txt, $die=true) {
 echo "\n<div class='myerror'><b>ERROR:</b> $txt</div>\n";
 if ($die) {
   wolFooter();
   die("<!-- myerror() -->");
 }
}

?>