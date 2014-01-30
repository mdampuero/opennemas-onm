<?php
/*
 * -------------------------------------------------------------
 * File:     	function.humandate.php
 */
function smarty_function_humandate($params, &$smarty) {
    $created = $params['created'];
    $updated = $params['updated'];

    $article = isset($params['article'])? $params['article']: null;
    $starttime = strtotime($article->starttime);

    if(!empty($starttime)) {
        return humandate($starttime);
    }

    if(preg_match('/\-/', $created)) {
        $created = strtotime($created);
    }

    if( empty($updated) || preg_match('/^0000\-00\-00/', $updated) ) {
        // 11/03/09 |  01:58 h
        return humandate($created);
    }

    if(preg_match('/\-/', $updated)) {
        $updated = strtotime($updated);
    }

    return humandate($updated);
}

/**
 *
 * @link http://blog.evandavey.com/2008/04/php-date-in-human-readable-form-facebook-style.html
 */
function humandate($timestamp){
    $difference = time() - $timestamp;
    $periods = array("segundo", "minuto", "hora", "dia", "semana", "mes", "año", "decada");
    $lengths = array("60",  "60",  "24",   "7",   "4.35",   "12",  "10");

    if ($difference > 0) { // this was in the past
        $humantext = "hace";
    } else { // this was in the future
        $difference = -$difference;
        $humantext = "quedan";
    }
    for($j = 0; $difference >= $lengths[$j]; $j++) {
        if($lengths[$j] == 0) {
            return '';
        }
        $difference /= $lengths[$j];
    }

    $difference = round($difference);
    if($difference != 1) {
        if($periods[$j] == 'mes') {
            $periods[$j] .= 'es';
        } else {
            $periods[$j] .= 's';
        }
    }
    $text = "$humantext $difference $periods[$j] ";
    return $text;
}