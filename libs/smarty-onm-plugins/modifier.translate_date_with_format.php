<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_modifier_translate_date_with_format($date, $format = "l, j \d\\e F")
{
    if (!is_null($date) && !is_string($date)) {
        return '';
    }

    try {
        $date = new DateTime($date);
    } catch (Exception $e) {
        return '';
    }

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
        "Domingo", "Luns", "Martes", "Mércores", "Xoves", "Vernes", "Sábado",
        "Xan", "Feb", "Mar", "Abr", "Mai", "Xuñ", "Xul",
        "Ago", "Sep", "Out", "Nov", "Dec",
        "Dom", "Lun", "Mar", "Mer", "Xov", "Ver", "Sab"
    ];

    $str    = $date->format($format);
    $locale = getService('core.locale')->getLocale();

    if ($locale == 'es_ES' || $locale == 'es') {
        $str = preg_replace($namesEN, $namesES, $str);
    } elseif ($locale == 'gl_ES' || $locale == 'gl') {
        $str = preg_replace($namesEN, $namesGL, $str);
    } else {
        $str = preg_replace('/\s+de\s+/', ' ', $str);
    }

    return $str;
}
