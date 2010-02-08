<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<style>
body { font-family: arial; font-size: 12px;}
</style>
</head>

<body>

<?php
    
  $link = mysql_connect('localhost', '1m2009', 'zs27zps44');
  mysql_select_db('1m2009');      

  function formulario_provincia($codigo_provincia,$nombre_provincia){
    echo "<form action='$PHP_SELF' method='post'>";
    echo "<b>$nombre_provincia</b>:<br>";
    echo "<select name='concello' style='width:200'>";
    $con_coruna=mysql_query("select * from concellos where codigo_provincia='$codigo_provincia'");
    while ($res_coruna=mysql_fetch_object($con_coruna)){
      echo "<option value='$res_coruna->codigo_concello' >$res_coruna->nombre_concello";    
    }
    echo "</select>&nbsp;";
    echo "<input type='submit' name='opc' value='Ver'>";
    echo "</form>";
  }
  
  function principal(){
    echo "Seleccione provincia/concello:<br><br>";
    formulario_provincia("15","A Coru&ntilde;a");
    formulario_provincia("27","Lugo");
    formulario_provincia("32","Ourense");
    formulario_provincia("36","Pontevedra");
    echo "<br /><br />";
    echo "<img width='265' src='http://www.xornal.com/media/images/galicia/20090301/2009030122253864276.jpg'/>";
  }

  
  function muestraconcello($codigo){
    $con_concello=mysql_query("select * from concellos where codigo_concello='$codigo'");
    $res_concello=mysql_fetch_object($con_concello);
    echo "<b>$res_concello->nombre_concello</b>";
    echo "<br><br>";
    echo "Censo: <b>$res_concello->censo_concello</b><br>";
    echo "Votos: <b>$res_concello->votos_concello</b><br>";
    echo "Nulos: <b>$res_concello->nulos_concello</b><br>";
    echo "En branco: <b>$res_concello->brancos_concello</b><br>";
    echo "<br><br>";
    echo "<table width=200 style='font-size: 11px; font-family: arial'>";
    echo "<tr bgcolor='#e4ddc9'>";
    echo "<td width=140>Partido<td>Votos";
    $con_votos=mysql_query("select * from resultados_concellos where nombre_concello='$res_concello->nombre_concello' and codigo_provincia='$res_concello->codigo_provincia' and votos>0 order by votos desc");
    while ($res_votos=mysql_fetch_object($con_votos)){
      echo "<tr><td>$res_votos->abreviatura_partido</td>";
      echo "<td align='right'>$res_votos->votos</td>";
    }
    echo "<tr>";
    echo "</table>";
    echo "<br><br>";
    echo "<a href='?'>Voltar</a>";
        
  }
  
  
  switch($_POST[opc]){
    case "Ver":
      muestraconcello($_POST[concello]);
      break;
     default:
      principal();
      break;  
  }
?>
</body>
</html>
<html><body></body></html>