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

echo "<h2>Zapnutie PC</h2>";
back();
echo "<p class='help'>";
echo "Pr�slu�n�m po��ta�om bol poslan� paket, ktor� by ich mal zapn�� (ak podporuj� WakeOnLan).<br>";
echo "Vo v�pise zapnut�ch po��ta�ov sa to m��e prejavi� a� po min�te, ke� nabehne Windows...";
echo "</p>\n";
$out="<UL>\n";
$err=$ok=0;
foreach($_POST as $index => $on) if (preg_match("/^ip_/", $index)) {
 $aIP=explode("_", $index);
 array_shift($aIP);
 $ip=implode(".", $aIP);
 $ret=$wol->sendMagic($IPdata[$ip]['mac']);
 $out.="<li><b>$ip</b>: ";
 if ($ret) $out.="<font style=\"color: red;\">CHYBA: $ret</font>\n";
      else $out.="<font style=\"color: green;\">Paket na zobudenie poslan�</font>\n";
}
$out.="</UL>\n";


echo $out;
back();

wolFooter();
?>
