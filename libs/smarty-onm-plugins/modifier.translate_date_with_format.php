<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_translate_date_with_format($date, $format = "L, j de F")
{
    $namesEN = [
        "/January/", "/February/", "/March/", "/April/", "/May/", "/June/",
        "/July/", "/August/", "/September/", "/October/", "/November/", "/December/",
        "/Sunday/", "/Monday/", "/Tuesday/", "/Wednesday/", "/Thursday/", "/Friday/", "/Saturday/",
        "/Jan/", "/Feb/", "/Mar/", "/Apr/", "/May/", "/Jun/",
        "/Jul/", "/Aug/", "/Sep/", "/Oct/", "/Nov/", "/Dec/",
        "/Sun/", "/Mon/", "/Tue/", "/Wed/", "/Thu/", "/Fri/", "/Sat/",
    ];

    $namesES = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
        "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre",
        "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado",
        "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul",
        "Ago", "Sep", "Oct", "Nov", "Dic",
        "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab",
    ];

    $namesGL = [
        "Xaneiro", "Febreiro", "Marzo", "Abril", "Maio", "Xuño", "Xullo",
        "Agosto", "Septembro", "Outubro", "Novembro", "Decembro",
        "Domingo", "Lúns", "Martes", "Mércores", "Xoves", "Vernes", "Sábado",
        "Xan", "Feb", "Mar", "Abr", "Mai", "Xuñ", "Xul",
        "Ago", "Sep", "Out", "Nov", "Dec",
        "Dom", "Lun", "Mar", "Mer", "Xov", "Ver", "Sab"
    ];

    $datetime = new DateTime($date);
    $dateEn = $datetime->format($format);

    $locale = getService('core.locale')->getLocale();
    if ($locale == 'es_ES' || $locale == 'es') {
        $dateR = preg_replace($namesEN, $namesES, $dateEn);
    } elseif ($locale == 'gl_ES' || $locale == 'gl') {
        $dateR = preg_replace($namesEN, $namesGL, $dateEn);
    } else {
        $dateR = preg_replace('/de/', '', $dateEn);
    }

    return $dateR;
}
