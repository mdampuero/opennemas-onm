<?php
/**
 * smarty_insert_time, Smarty insert plugin to generate the time string
 * <code>
 * {time}
 * </code>
 *
 * @param array $params  Parameters of smarty function
 * @param \Smarty $smarty Object reference to Smarty class
 *
 * @return string
 */
function smarty_function_time($params, &$smarty)
{
    $arrMonth = [
        "enero", "febrero", "marzo", "abril", "mayo",
        "junio", "julio", "agosto", "septiembre",
        "octubre", "noviembre", "diciembre"
    ];
    $arrDay   = [
        "Domingo", "Lunes", "Martes", "Miércoles",
        "Jueves", "Viernes", "Sábado"
    ];

    return sprintf(
        '<span class="hour">%s h.</span> <span class="day">%s, %s de %s de %s</span>',
        date('G:i'),
        $arrDay[date("w")],
        date("d"),
        $arrMonth[date("n") - 1 ],
        date("Y")
    );
}
