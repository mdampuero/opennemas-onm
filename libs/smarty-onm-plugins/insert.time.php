<?php
/**
 * insert.time.php, Smarty insert plugin to generate the time
 *
 * @package  OpenNeMas
 * @author Toni Martínez <toni@openhost.es>
 * @version  0.6-rc1
 */

/**
 * smarty_insert_time, Smarty insert plugin to generate the time
 * <code>
 * {insert name="time"}
 * </code>
 *
 * @author Toni Martínez <toni@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return string Return a HTML code of the rating bar
 */
function smarty_insert_time($params, &$smarty)
{
    $arrMonth = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
    $arrDay = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");

    return   '<span class="hour">'.date ('G:i'). ' h.</span> <span class="day">'. $arrDay[date("w")].', '.date ("d"). ' de ' .$arrMonth[date("n")-1]. ' de ' .date("Y").'</span>';
}
?>