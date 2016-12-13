<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.png">

    <title>Prender maquinas</title>

    <!-- Bootstrap core CSS -->
    <link href="../bootstrap/dist/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- <link href="jumbotron-narrow.css" rel="stylesheet"> -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
  
  <?php
  require("include.php");
  require("WoL-v2.php");
  $conn=DBConnect();
  $WoL=new WakeOnLan();


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
    if (!eregi("^".$WoL->regex_ip."$", "$ip_pref.$ip_from")) myerror("Zadali ste nesprávnu prvú IP addresu!");
    if (!eregi("^".$WoL->regex_ip."$", "$ip_pref.$ip_to"  )) myerror("Zadali ste nesprávnu poslendú IP addresu!");
    if ($ip_from > $ip_to) myerror("Prvá IP adresa je väèšia ako posledná!");
    for($i=$ip_from; $i <= $ip_to; $i++) $manualIPs[]="$ip_pref.$i";
  }

  /*** PING IPs ***/
  if ($manual) $ping=$WoL->ping($manualIPs);
          else $ping=$WoL->ping($IPs);

  ?>

  <body>

    <div class="container">
  
		  <div class="page-header">
			  <h1>Encender máquinas<small> del estudio Sisti</small></h1>
		  </div>
      
      <div id="tabs" class="container"> 
        <ul class="nav nav-tabs">
              <li class="active">
                <a  href="#1" data-toggle="tab">Equipos existentes</a>
               <br>
               <?php

  echo "<FORM ACTION=\"wakeup.php\" METHOD=\"post\">";
  echo "\n<TABLE class=\"table\">";
  echo "\n <TR>";
  echo "<th>";
  if ($sorted=="name") echo $arrowDown;
  echo "&nbsp;<a class='sort' href='./?sort=name'>Equipo</a>&nbsp;";
  echo "<th>";
  if ($sorted=="ip") echo $arrowDown;
  echo "&nbsp;<a class='sort' href='./?sort=ip'>IP</a>&nbsp;";
  echo "<th>&nbsp;Estado&nbsp;";
  echo "<th>&nbsp;Encender?&nbsp;";
  foreach($IPs as $ip) { // 
   echo "\n <TR>";
   echo "<td>&nbsp;".$IPdata[$ip]['name']."&nbsp;";
   echo "<td>&nbsp;".$ip."&nbsp;";
   echo "<td>";
   if (!$ping[$ip]) 
     echo '<span class="label label-success">Encendido</span>';
   else if ($ping[$ip]=="no data received")
     echo '<span class="label label-warning">Apagado</span>';
   else echo $ping[$ip];
   echo "&nbsp;";
   $ip2="ip_".str_replace('.', '_', $ip);
   echo "<td align=\"center\"><input type='checkbox' name='$ip2' $w>";
  }
  echo "\n</TABLE>\n";
  echo "<br><INPUT TYPE='submit' name='btn3' value='Encender'>\n";
  echo "</FORM>\n";

                ?>

              </li>
              <li><a href="#2" data-toggle="tab">Agregar nueva</a>
              </li>
        </ul>

        <div class="tab-content ">
          <div class="tab-pane active" id="1">
            

          </div>
          <div class="tab-pane" id="2">
          

          </div>
        </div>
      </div>

      <div class="footer">
        <p>2015</p>
      </div>

    </div> <!-- /container -->

  </body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</html>
