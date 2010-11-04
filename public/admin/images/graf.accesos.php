<?php
require(SITE_LIBS_PATH.'utils.functions.php');
require_once(SITE_LIBS_PATH.'jpgraph/jpgraph.php');
require_once(SITE_LIBS_PATH.'jpgraph/jpgraph_line.php');
require_once(SITE_LIBS_PATH.'Calendar/Month/Weekdays.php');
require_once(SITE_LIBS_PATH.'log.class.php');

import_libs('*');

// Controlar el role admin
session_admin();

$mes = (isset($_GET['mes']) && ($_GET['mes'] < 13) && ($_GET['mes'] > 0))? $_GET['mes']: date("n");
$mes_actual = date("n");
$anho = ($mes > $mes_actual) ? (date("Y")-1): date("Y");
$mes_nombre = array('','Enero', 'Febrero', 'Marzo','Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre','Octubre', 'Noviembre', 'Diciembre');
$fechas = array();

$Month = new Calendar_Month($anho, $mes);
$Month->build();
while ($Day = $Month->fetch()) {
    $fechas[$Day->thisDay()] = 0;
}

$cfg = new hebblog();
$lineas = $cfg->parsear_csv_log('access_log.csv');

foreach ($lineas as $v) {
	$partes = explode('/',$v[0]);
	$anos   = explode(' ',$partes[2]);
	if(($partes[1] == $mes)&&($anos[0] == $anho)) {
		$fechas[(int)$partes[0]] += 1;
	}
}

$ydata = array_values($fechas);

// Crear el gr�fico
$graph = new Graph(300,200,"auto");
$graph->SetScale("textint");

// Configuraci�n de margen y t�tulos
$graph->img->SetMargin(40,20,20,40);
$graph->title->Set("Visitas en: ".$mes_nombre[$mes]." de ".$anho);
$graph->title->SetFont(FF_ARIAL,FS_NORMAL,11);
$graph->yaxis->HideTicks(true,false);
$graph->xaxis->title->Set("D�a");
$graph->yaxis->title->Set("Visitas");
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->xaxis->SetTextLabelInterval(2);
$graph->xaxis->SetTickLabels(array_keys($fechas));

// Crear el linear plot
$lineplot=new LinePlot($ydata);

// A�adir el plot al gr�fico
$graph->Add($lineplot);

// Visualizar el gr�fico
$graph->Stroke();
?>