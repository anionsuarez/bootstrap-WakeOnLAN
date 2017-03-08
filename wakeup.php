<?php
require("include.php");
require("WoL-v2.php");
$wol=new WakeOnLan;
$conn=DBConnect();

/*** GET IPs from DB ***/
$res=$conn->query("SELECT * FROM WakeOnLan");
if (!$res) die("Error al obtener datos de la base de datos");
$IPdata=array();
while ($row=$res->fetch(PDO::FETCH_ASSOC)) {
  $IPdata[$row['ip']]['name']=$row['name'];
  $IPdata[$row['ip']]['mac' ]=$row['mac'];
}

//echo "<h2>Encender PC</h2>";
//back();
echo "<p class='help'>";
/*echo "Se envió el paquete mágico, si la computadora esta bien configurada se va a prender.<br>";
echo "Deberá esperar unos minutos a que el equipo arranque...";
echo "</p>\n";*/
$out="<UL>\n";
$err=$ok=0;  
foreach($_POST as $index => $on) if (preg_match("/^ip_/", $index)) {
 $aIP=explode("_", $index); //corto un string en dos array cuanto veo "_"
 array_shift($aIP); //Quita el primer valor del array y lo devuelve
 $ip=implode(".", $aIP);
 $ret=$wol->sendMagic($IPdata[$ip]['mac']);
 $out.="<li><b>$ip</b>: ";
 if ($ret) $out.="<font style=\"color: red;\">CHYBA: $ret</font>\n";
      else $out.="<font style=\"color: green;\">Se envi&oacute; el paquete</font>\n";
    $out.="</li>";
}
$out.="</UL></p>";

echo $out;
?>
