<?php

class WakeOnLan {
  var $ping_port="time";
  var $ping_recv=100; // mili-secs
  var $ping_try =10;  // mili-secs
  var $arp="/usr/sbin/arp";

function WakeOnLan() { // constructor
 $this->numb="[0-9]{1,3}";
 $this->mac1="[0-9a-f]{1,2}";
 $this->regex_ip =str_repeat($this->numb."\\.", 3) . "$this->numb";
 $this->regex_mac=str_repeat($this->mac1.":"  , 5) . "$this->mac1";
}

function sendMagic($mac, $ip="255.255.255.255", $port=9) {
 // will send WakeOnLan magic sequence encapsulated in UDP port 9 (discard) datagram
 // WoL magic sequence may be anywhere within ethernet frame received by NIC
 // in our case this will be:
 // [ethernet header][IP header][UDP header][Magic sequence][CRCS]
 // magic sequence = 6x 0xFF + 16x MAC-address of NIC to wake up
 // return 0 on success, error description otherwise

 // prepare magic sequence
 if (!preg_match("/^$this->regex_mac$/i", $mac)) return "[$mac/$ip]: MAC address in unknown format";
 $data=str_repeat(chr(0xFF), 6);
 $aMAC=explode(":", $mac); // array of MAC address parts
 $MACs="";
 foreach($aMAC as $part) $MACs.=chr(intval($part,16)); // MAC address in binary form ($part is number in hex)
 $data.=str_repeat($MACs, 16); // magic sequence ready

 //$port=getservbyname("discard", "udp");
 $sock=@socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
 if (!$sock) return "[$mac/$ip]: socket_create():".socket_last_error($sock);
 socket_setopt($sock, SOL_SOCKET, SO_BROADCAST, 1);
 $res=@socket_sendto($sock, $data, strlen($data), 0x100, $ip, $port); // 0x100 = Data completes transaction
 if ($res === false) return "[$mac/$ip]: socket_sendto():".socket_last_error($sock);
 return 0; // everything seems OK
}


/*function Ping($aIP) {
 // ping each IP in array and return results in assoc. array (key is IP)
 // array[$ip]==0 if IP is alive, error desc. otherwise
 $ctimeout=0.1;      // connect timeout in (float)secs (probably useless for UDP)
 $utimeout=1000*$this->ping_recv; // receive timeout in micro-seconds
 $usleept =1000*$this->ping_try;  // attempt to receive every $usleept (until $utimeout)
 //$data=str_repeat(".", 24);
 $data=chr(13);
 $prot="udp";
 $port=getservbyname($this->ping_port, $prot);
 foreach ($aIP as $ip) { // Para cada IP
  echo $ip."<br>";
   $errno=$errstr="";
   $sock=@fsockopen("$prot://$ip", $port, $errno, $errstr, $ctimeout);
   if (!$sock) $ret[$ip]="connect error #$errno: $errstr";
   if (@socket_set_blocking($sock, false) === false) die("can't sent non-blocking mode for socket");
   if (@fputs($sock, $data, strLen($data)) === false) {
     $ret[$ip]="error writing to socket";
     fclose($sock);
     break; // go to next IP
   }
   for($i=$usleept; $i<$utimeout; $i+=$usleept) { // every $usleept until $utimeout
     usleep($usleept); // wait for data
     $retdata=@fgets($sock, 1);

     if ($retdata == false) {
       // probably because of port unreachable ==> IP should be alive
       //$ret[$ip]="error in fgets(), time $i";
       $ret[$ip]=0;
       break;
     }
     if ($retdata) { $ret[$ip]=0; break; } // received something
   }
   if ($retdata === "") $ret[$ip]="no data received";
       fclose($sock);
 }
 return $ret;
}*/

function Ping($aIP) {
 foreach ($aIP as $ip) { 
    $ret[$ip]=0;
    $ping_result = shell_exec('ping -c 1 '.$ip); //el -c 1 indica que pingueo una vez
    if( !preg_match('/ 1 received/',$ping_result) ){
       $ret[$ip]=1;
        // goto next1;
     }
   }
       return $ret;
} 

function getFromARP() {
 // try to get IP to MAC mapping from ARP cache
 // return array[IP]=MAC
 //        or error string
 exec("$this->arp -nv", $a_resp, $ret_val);
 switch($ret_val) {
  case 127: return "$this->arp not found, cannot be executed!";
  case   0: break;
  default : return "Unknown error while running $this_arp (return code $ret_val)!";
 }
 $regex="/($this->regex_ip) .* ($this->regex_mac) /";
 foreach($a_resp as $line) if (preg_match($regex, $line, $regs))
   $ret[$regs[1]]=$regs[2];
 return $ret;
}


} /* class */

?>