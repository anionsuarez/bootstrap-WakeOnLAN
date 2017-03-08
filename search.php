<?php
require("include.php");
require("WoL-v2.php");
$WoL=new WakeOnLan();




/*** GET IPs from user-supplied range ***/
  $ip_pref =$_GET['ip1'].".";
  $ip_pref.=$_GET['ip2'].".";
  $ip_pref.=$_GET['ip3'];
  $ip_from =$_GET['ip4'];
  $ip_to   =$_GET['ip5'];
  if (!$ip_to) $ip_to=$ip_from; // only 1 IP address (not range)
  //if (!eregi("^".$WoL->regex_ip."$", "$ip_pref.$ip_from")) myerror("Zadali ste nesprávnu prvú IP addresu!");
  //if (!eregi("^".$WoL->regex_ip."$", "$ip_pref.$ip_to"  )) myerror("Zadali ste nesprávnu poslendú IP addresu!");
  if (!preg_match("/^".$WoL->regex_ip."$/", "$ip_pref.$ip_from")) myerror("Dirección IP incorrecta");
  if (!preg_match("/^".$WoL->regex_ip."$/", "$ip_pref.$ip_to"  )) myerror("Error en el ultimo campo");
  if ($ip_from > $ip_to) myerror("Prvá IP adresa je väèšia ako posledná!");
  for($i=$ip_from; $i <= $ip_to; $i++) $manualIPs[]="$ip_pref.$i";

  $ping=$WoL->ping($manualIPs);
 

$arp=$WoL->getFromARP();
  if (!is_array($arp)) myerror($q);
  //back();
 
/*  echo "<p class='help'>";
  echo "Èervené pozadie znamená, že daný poèítaè je vypnutý a nie je možné zisti jeho MAC adresu.<br>&nbsp;<br>";
  echo "Šedé pozadie znamená, že poèítaè sa už nachádza v databáze.<br>&nbsp;<br>";
  echo "Zelené pozadie znamená, že poèítaè je zapnutý a ešte sa nenachádza v databáze.<br>Ak sa podarilo zisti jeho MAC adresu, je možné ho prida. Je potrebné vypni <i>Názov</i>, pod ktorým bude v databáze uložený.";
  echo "</p>\n";
  echo "<h2 style=\"margin-top: -10px;\">Pridanie poèítaèa do databázy</h2>\n";*/
  echo "<FORM ACTION='add-ip.php' METHOD='post'>\n";
  echo "\n<TABLE cellpadding=1 class=\"nb\" align=\"center\">";
  echo "\n <TR><th class=\"nb\">Direccion IP<th class=\"nb\">Nombre de pc<th class=\"nb\">MAC address<th class=\"nb\">Estado";
  
  foreach($manualIPs as $ip) { // pre kazdu IP riadok tabulky
   if (isset($arp[$ip])) $txt="'$arp[$ip]' readonly";
                    else $txt="''";
   if ($ping[$ip]) { $pic="off"; $col="red"; }
              else { $pic="on";  $col="limegreen"; }
   if (isset($IPdata[$ip]) && $IPdata[$ip]['name']) { // IP uz je v DB
     $name="'".$IPdata[$ip]['name']."' readonly";
     $txt ="'".$IPdata[$ip]['mac'] ."' readonly";
     $col ="gray";
   } else $name="'?'";
   echo "\n <TR>";
   echo "<td class=\"nb\"><INPUT TYPE='text' name='add_ip[]'   value='$ip' readonly style=\"background-color: $col;\">";
   echo "<td class=\"nb\"><INPUT TYPE='text' name='add_name[]' value=$name style=\"background-color: $col;\">";
   echo "<td class=\"nb\"><INPUT TYPE='text' name='add_mac[]'  value=$txt style=\"background-color: $col;\">";
   echo "<td class=\"nb\"><img src='$pic.gif' alt='Stav' border=0>";
   if (!$ping[$ip]) echo "OK";
   else if ($ping[$ip]=="no data received")
        echo "vypnutý";
   else echo $ping[$ip];
  } /* foreach() */
  echo "\n</TABLE>\n";
  echo "<br><center><INPUT TYPE='submit' name='btn2' value='Pridaj'></center>\n";
  echo "</FORM>\n";

 ?>