<?php
require("include.php");
require("WoL-v2.php");
$wol=new WakeOnLan;
$conn=DBConnect();

/*** GET IPs from DB ***/
$res=$conn->query("SELECT * FROM WakeOnLan");
if (!$res) die("Error getting data from database!");
$IPdata=array();
while ($row=$res->fetch(PDO::FETCH_ASSOC)) {
  $IPdata[$row['ip']]['name']=$row['name'];
  $IPdata[$row['ip']]['mac' ]=$row['mac'];
}
// prepare array for ping
foreach($IPdata as $key => $val) $IPs[]=$key;

echo "<h2>Pridanie PC do databázy</h2>";
back();
$out="<UL>\n";
$err=$ok=0;
foreach($_POST['add_ip'] as $index => $ip) {
  $out.=" <li><b>$ip</b>: ";
  if (isset($IPdata[$ip])) { $out.="Ya existe en la base de datos\n"; continue; }
  if (!$_POST['add_mac'][$index]) { $out.="No se pudo encontrar la MAC, posiblemente este apagada\n"; continue; }
  if (!preg_match("/^$wol->regex_mac$/i", $_POST['add_mac'][$index])) { $out.="<font style=\"color: red;\">Mac introducida de manera incorrecta</font>\n"; $err++; continue; }
  if ($_POST['add_name'][$index]=='?' ||
     !$_POST['add_name'][$index]) { $out.="<font style=\"color: red;\">PC sin nombre</font>\n"; $err++; continue; }
  $q ="INSERT INTO WakeOnLan (ip,name,mac) VALUES('$ip',";
  $q.="'".$_POST['add_name'][$index]."',";
  $q.="'".$_POST['add_mac'][$index]."')";
  $res=$conn->query($q);
  if (!$res) { $out.="Error al guardar la base de datos: ".mysql_error()."\n"; $err++; }
  else { $out.="<b style=\"color: green;\">Se guardo en la base de datos!</b>\n"; $ok++; }
}
$out.="</UL>\n";

echo "Nro de error: ";
if ($err) echo "<b style=\"color: red;\">$err</b>";
     else echo $err;
echo "<br>Agregado con éxito: ";
if ($ok ) echo "<b style=\"color: green;\">$ok</b>";
     else echo $ok;

echo $out;
back();
?>