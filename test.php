<?php
$mac ="00:xx:xx:xx:xx:xx";
$ip  ="marki.no-ip.org";
$port=4121;

require("WoL-v2.php");
$WoL=new WakeOnLan();
echo $WoL->sendMagic($mac, $ip, $port);

?>