<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
use Onm\Settings as s;

function smarty_modifier_translate_date_with_format($date,  $format = "L, j de F") {

    $namesEN = array("/January/","/February/","/March/","/April/","/May/","/June/","/July/",
                    "/August/","/September/","/October/","/November/","/December/",
                    "/Sunday/","/Monday/","/Tuesday/","/Wednesday/","/Thursday/","/Friday/","/Saturday/",
                    "/Jan/","/Feb/","/Mar/","/Apr/","/May/","/Jun/","/Jul/",
                    "/Aug/","/Sep/","/Oct/","/Nov/","/Dec/",
                    "/Sun/","/Mon/","/Tue/","/Wed/","/Thu/","/Fri/","/Sat/",
                );


    $namesES = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                    "Agosto","Septiembre","Octubre","Noviembre","Diciembre",
                    "Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado",
                    "Ene","Feb","Mar","Abr","May","Jun","Jul",
                    "Ago","Sep","Oct","Nov","Dic",
                    "Dom","Lun","Mar","Mie","Jue","Vie","Sab",
                );

    $namesGL = array("Xaneiro","Febreiro","Marzo","Abril","Maio","Xuño","Xullo",
                    "Agosto","Septembro","Outubro","Novembro","Decembro",
                    "Domingo","Lúns","Martes","Mércores","Xoves","Vernes","Sábado",
                    "Xan","Feb","Mar","Abr","Mai","Xuñ","Xul",
                    "Ago","Sep","Out","Nov","Dec",
                    "Dom","Lun","Mar","Mer","Xov","Ver","Sab"
                );


    $format_date = new DateTime($date);

    $dateEn = $format_date->format($format);

    $locale = s::get('site_language');

    if ( $locale == 'es_ES' ) {
        $dateR = preg_replace($namesEN, $namesES, $dateEn);

    } elseif ( $locale == 'gl_ES' ) {
        $dateR = preg_replace($namesEN, $namesGL, $dateEn);
    } else {
        $dateR = $dateEn;
    }

    return $dateR;

}

