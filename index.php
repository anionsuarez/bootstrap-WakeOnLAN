<?php
require("include.php");
require("WoL-v2.php");
wolHeader();
$conn=DBConnect();
$WoL=new WakeOnLan();
echo "<h1>Stav sledovan�ch po��ta�ov</h1>\n";

// sorting
if (isset($_GET['sort'])) $sorted=$_GET['sort'];
                     else $sorted="name";
if (!preg_match('/^(name|ip)$/', $sorted)) $sorted="name";

/*** GET IPs from DB ***/
$res=mysql_query("SELECT * FROM WakeOnLan ORDER BY '$sorted'");
if (!$res) die("Error getting data from database!");
$IPdata=array();
while ($row=mysql_fetch_assoc($res)) {
  //print_r($row);
  $IPdata[$row['ip']]['name']=$row['name'];
  $IPdata[$row['ip']]['mac' ]=$row['mac'];
}
// prepare array for ping
foreach($IPdata as $key => $val) $IPs[]=$key;
//print_r($IPs);

/*** GET IPs from user-supplied range ***/
if (isset($_GET['manualip'])) $manual=$_GET['manualip'];
                         else $manual=false;
if ($manual) {
  $ip_pref =$_GET['ip1'].".";
  $ip_pref.=$_GET['ip2'].".";
  $ip_pref.=$_GET['ip3'];
  $ip_from =$_GET['ip4'];
  $ip_to   =$_GET['ip5'];
  if (!$ip_to) $ip_to=$ip_from; // only 1 IP address (not range)
  if (!eregi("^".$WoL->regex_ip."$", "$ip_pref.$ip_from")) myerror("Zadali ste nespr�vnu prv� IP addresu!");
  if (!eregi("^".$WoL->regex_ip."$", "$ip_pref.$ip_to"  )) myerror("Zadali ste nespr�vnu poslend� IP addresu!");
  if ($ip_from > $ip_to) myerror("Prv� IP adresa je v��ia ako posledn�!");
  for($i=$ip_from; $i <= $ip_to; $i++) $manualIPs[]="$ip_pref.$i";
}

/*** PING IPs ***/
if ($manual) $ping=$WoL->ping($manualIPs);
        else $ping=$WoL->ping($IPs);

/*** in NORMAL mode, display PING results with links to WakeUp ***/
if (!$manual) {
  $cnt_all=count ($IPs);
  $cnt_err=countx($ping);
  echo "<TABLE class='Tsmall' cellpadding=2 align=\"center\">";
  echo "\n <TR><td class='Tsmall'>&nbsp;V�etky  PC&nbsp;<td class='Tsmall' align='right'>&nbsp;<b>$cnt_all</b>&nbsp;";
  echo "\n <TR><td class='Tsmall'>&nbsp;Zapnut� PC&nbsp;<td class='Tsmall' align='right'>&nbsp;<b style=\"color: green;\">".($cnt_all-$cnt_err)."</b>&nbsp;";
  echo "\n <TR><td class='Tsmall'>&nbsp;Vypnut� PC&nbsp;<td class='Tsmall' align='right'>&nbsp;<b style=\"color: red;\"  ><b style=\"color: red;\">$cnt_err</b>&nbsp;";
  echo "\n</TABLE>\n";

  back('Obnovi�');
  echo "<p class='help'>";
  echo "Pre zmenu triedenia kliknite na pr�slu�n� st�pec tabu�ky<br>";
  echo "";
  echo "</p>\n";

  echo "<FORM ACTION=\"wakeup.php\" METHOD=\"post\">";
  echo "\n<TABLE cellpadding=1 align=\"center\">";
  echo "\n <TR>";
  echo "<th>";
  if ($sorted=="name") echo $arrowDown;
  echo "&nbsp;<a class='sort' href='./?sort=name'>Meno po��ta�a</a>&nbsp;";
  echo "<th>";
  if ($sorted=="ip") echo $arrowDown;
  echo "&nbsp;<a class='sort' href='./?sort=ip'>IP adresa</a>&nbsp;";
  echo "<th>&nbsp;Stav&nbsp;";
  echo "<th>&nbsp;Zapn��?&nbsp;";
  foreach($IPs as $ip) { // pre kazdu IP riadok tabulky
   if ($ping[$ip]) { $pic="off"; $w="checked"; $col="red"; }
              else { $pic="on";  $w="";  $col="limegreen"; }
   echo "\n <TR>";
   echo "<td>&nbsp;".$IPdata[$ip]['name']."&nbsp;";
   echo "<td>&nbsp;".$ip."&nbsp;";
   echo "<td><img src='$pic.gif' alt='Stav' border=0>";
   if (!$ping[$ip]) echo "OK";
   else if ($ping[$ip]=="no data received")
        echo "vypnut�";
   else echo $ping[$ip];
   echo "&nbsp;";
   $ip2="ip_".str_replace('.', '_', $ip);
   echo "<td align=\"center\"><input type='checkbox' name='$ip2' $w>";
  }
  echo "\n</TABLE>\n";
  echo "<br><center><INPUT TYPE='submit' name='btn3' value='Zapn��'></center>\n";
  echo "</FORM>\n";
/*** if MANUAL, get MACs and display add form ***/
} else {
  $arp=$WoL->getFromARP();
  if (!is_array($arp)) myerror($q);
  back();
  echo "<p class='help'>";
  echo "�erven� pozadie znamen�, �e dan� po��ta� je vypnut� a nie je mo�n� zisti� jeho MAC adresu.<br>&nbsp;<br>";
  echo "�ed� pozadie znamen�, �e po��ta� sa u� nach�dza v datab�ze.<br>&nbsp;<br>";
  echo "Zelen� pozadie znamen�, �e po��ta� je zapnut� a e�te sa nenach�dza v datab�ze.<br>Ak sa podarilo zisti� jeho MAC adresu, je mo�n� ho prida�. Je potrebn� vypni� <i>N�zov</i>, pod ktor�m bude v datab�ze ulo�en�.";
  echo "</p>\n";
  echo "<h2 style=\"margin-top: -10px;\">Pridanie po��ta�a do datab�zy</h2>\n";
  echo "<FORM ACTION='add-ip.php' METHOD='post'>\n";
  echo "\n<TABLE cellpadding=1 class=\"nb\" align=\"center\">";
  echo "\n <TR><th class=\"nb\">IP adresa<th class=\"nb\">Meno po��ta�a<th class=\"nb\">MAC adresa<th class=\"nb\">Stav";
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
        echo "vypnut�";
   else echo $ping[$ip];
  } /* foreach() */
  echo "\n</TABLE>\n";
  echo "<br><center><INPUT TYPE='submit' name='btn2' value='Pridaj'></center>\n";
  echo "</FORM>\n";
}

if (!$manual) { ?>
<center><FORM ACTION="./" METHOD="GET">
 Manu�lne prida� IP adresy z rozsahu:<br>
 <INPUT TYPE="text" NAME="ip1" size=3 maxlength=3> .
 <INPUT TYPE="text" NAME="ip2" size=3 maxlength=3> .
 <INPUT TYPE="text" NAME="ip3" size=3 maxlength=3> .
 <INPUT TYPE="text" NAME="ip4" size=3 maxlength=3> --
 <INPUT TYPE="text" NAME="ip5" size=3 maxlength=3>
 <INPUT TYPE="hidden" NAME="manualip" value="true"><br> <br>
 <INPUT TYPE="submit" name="btn1" value="Odosla�">
</FORM></center>
<?php }

wolFooter();
?>
