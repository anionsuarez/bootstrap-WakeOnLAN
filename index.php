<!DOCtype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.PNG">

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

  /*** get IPs from DB ***/
  $res=$conn->query("SELECT * FROM WakeOnLan");
  if (!$res) 
    die("Error obteniendo datos de la base!");
  $IPdata=array();
  while ($row=$res->fetch(PDO::FETCH_ASSOC)) {
    //print_r($row);
    $IPdata[$row['ip']]['name']=$row['name'];
    $IPdata[$row['ip']]['mac' ]=$row['mac'];
  }
  // prepare array for ping
  foreach($IPdata as $key => $val) $IPs[]=$key;
  //print_r($IPs);

  /*** get IPs from user-supplied range ***/
  if (isset($_get['manualip'])) $manual=$_get['manualip'];
                           else $manual=false;
  if ($manual) {
    $ip_pref =$_get['ip1'].".";
    $ip_pref.=$_get['ip2'].".";
    $ip_pref.=$_get['ip3'];
    $ip_from =$_get['ip4'];
    $ip_to   =$_get['ip5'];
    if (!$ip_to) $ip_to=$ip_from; // only 1 IP address (not range)
    if (!preg_match("/^".$WoL->regex_ip."$/", "$ip_pref.$ip_from")) myerror("Dirección IP incorrecta");
    if (!preg_match("/^".$WoL->regex_ip."$/", "$ip_pref.$ip_to"  )) myerror("direccion incorrecta ultima");
    if ($ip_from > $ip_to) myerror("La direccion es menor a la ingresada anteriormente");
    for($i=$ip_from; $i <= $ip_to; $i++) $manualIPs[]="$ip_pref.$i";
  }

  /*** PING IPs ***/
  if ($manual) $ping=$WoL->ping($manualIPs);
          else $ping=$WoL->ping($IPs);
  ?>

  <body>

    <div class="container">

      <div class="page-header">
        <h1>Encender máquinas<small> U.E.P.</small></h1>
      </div>

      <div class="panel with-nav-tabs">
        <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1default" data-toggle="tab">Equipos existentes</a></li>
                    <li><a href="#tab2default" data-toggle="tab">Agregar nueva</a></li>   
                </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">
            <div class="tab-pane fade in active" id="tab1default">
              <?php
                echo "<FORM id='form_encender' action=wakeup.php action=\"post\">";
                echo "\n<TABLE class=\"table\">";
                echo "\n <TR>";
                echo "<th>&nbsp;Equipo&nbsp;";
                echo "<th>&nbsp;IP&nbsp;";
                echo "<th>&nbsp;Estado&nbsp;";
                echo "<th>&nbsp;Encender?&nbsp;";
                foreach($IPs as $ip) {
                    if ($ping[$ip]) { $pic="off"; $w="checked"; $col="red"; }
                            else { $pic="on";  $w="";  $col="limegreen"; } 
                 echo "\n <TR>";
                 echo "<td>&nbsp;".$IPdata[$ip]['name']."&nbsp;";                 
                 echo "<td>&nbsp;".$ip."&nbsp;";
                 echo "<td>";
                 if ($ping[$ip]==0) 
                   echo '<span class="label label-success">Encendido</span>';
                 else if ($ping[$ip]==1)
                   echo '<span class="label label-danger">Apagado</span>';
                 else echo $ping[$ip];
                 echo "&nbsp;";
                 $ip2="ip_".str_replace('.', '_', $ip);
                 echo "<td align=\"center\"><input type='checkbox' name='$ip2' $w>";
                }
                echo "\n</TABLE>\n";
                echo '<button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_tab1">';
                echo '<span class="encender glyphicon glyphicon-off"></span> Encender';
                echo '</button>';
                echo "</FORM>\n";
              ?>
            </div>

          <!--  <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modal_tab1">
  Launch demo modal
</button> -->

            <div class="tab-pane fade" id="tab2default">
              <FORM class="form-inline" action="search.php" action="post">
               Ingrese una ip o un rango<br>
               <div class="form-group">
               <INPUT type="text" class="form-control col-md-1 text-center" name="ip1" size=3 maxlength=3>
               </div>
               <div class="form-group">
               <INPUT type="text" class="form-control col-md-1 text-center" name="ip2" size=3 maxlength=3>
               </div>
               <div class="form-group">
               <INPUT type="text" class="form-control col-md-1 text-center" name="ip3" size=3 maxlength=3>
               </div>
               <div class="form-group">
               <INPUT type="text" class="form-control col-md-1 text-center" name="ip4" size=3 maxlength=3>
               </div>
               <div class="form-group">
               <INPUT type="text" class="form-control col-md-1 text-center" name="ip5" size=3 maxlength=3>
               </div>

               <!-- BORRAR <INPUT type="hidden" name="manualip" value="true"><br> <br> -->
               <!-- <INPUT type="submit" name="btn1" value="Buscar"> -->
               <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_tab1">
                <span class="encender glyphicon glyphicon-search"></span> Buscar
                </button>
              </FORM>
            </div>
          </div>
        </div>
      </div>



    </div> <!-- /container -->

  </body>

<div id="modal_tab1" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Wol</h4>
      </div>
      <div class="modal-body">
        <!-- se muestra la info que devuelve test.php -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal_tab2" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Wol</h4>
      </div>
      <div class="modal-body">
        <!-- se muestra la info que devuelve test.php -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>


<script>
/* must apply only after HTML has loaded */
$(document).ready(function () {
    $("#form_encender").on("submit", function(e) {
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
        $.ajax({
            url: formURL,
            type: "POST",
            data: postData,
            success: function(data, textStatus, jqXHR) {
                //$('#modal_tab1 .modal-header .modal-title').html("Hola!");
                $('#modal_tab1 .modal-body').html(data);

            },
            error: function(jqXHR, status, error) {
                console.log(status + ": " + error);
            }
        });
        e.preventDefault();
    });
});
</script>
</html>
