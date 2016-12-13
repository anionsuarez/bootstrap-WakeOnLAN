<?php
require("include.php");
require("WoL-v2.php");
$wol=new WakeOnLan;
wolHeader();
$conn=DBConnect();

/*** GET IPs from DB ***/
$res=mysql_query("SELECT * FROM WakeOnLan");
if (!$res) die("Error getting data from database!");
$IPdata=array();
while ($row=mysql_fetch_assoc($res)) {
  $IPdata[$row['ip']]['name']=$row['name'];
  $IPdata[$row['ip']]['mac' ]=$row['mac'];
}
// prepare array for ping
foreach($IPdata as $key => $val) $IPs[]=$key;

echo "<h2>Pridanie PC do datab�zy</h2>";
back();
$out="<UL>\n";
$err=$ok=0;
foreach($_POST['add_ip'] as $index => $ip) {
  $out.=" <li><b>$ip</b>: ";
  if (isset($IPdata[$ip])) { $out.="v datab�ze sa u� nach�dza...\n"; continue; }
  if (!$_POST['add_mac'][$index]) { $out.="Nezn�ma MAC adresa!\n"; continue; }
  if (!preg_match("/^$wol->regex_mac$/i", $_POST['add_mac'][$index])) { $out.="<font style=\"color: red;\">Chybne zadan� MAC adresa!</font>\n"; $err++; continue; }
  if ($_POST['add_name'][$index]=='?' ||
     !$_POST['add_name'][$index]) { $out.="<font style=\"color: red;\">Nezadan� meno PC</font>\n"; $err++; continue; }
  $q ="INSERT INTO WakeOnLan (ip,name,mac) VALUES('$ip',";
  $q.="'".$_POST['add_name'][$index]."',";
  $q.="'".$_POST['add_mac'][$index]."')";
  $res=mysql_query($q);
  if (!$res) { $out.="CHYBA pri vkladan� do datab�zy: ".mysql_error()."\n"; $err++; }
  else { $out.="<b style=\"color: green;\">VLO�EN� do DATAB�ZY</b>\n"; $ok++; }
}
$out.="</UL>\n";

echo "Po�et ch�b: ";
if ($err) echo "<b style=\"color: red;\">$err</b>";
     else echo $err;
echo "<br>�spe�ne pridan�ch: ";
if ($ok ) echo "<b style=\"color: green;\">$ok</b>";
     else echo $ok;

echo $out;
back();

wolFooter();
?>