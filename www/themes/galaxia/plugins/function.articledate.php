<?php
/*
 * -------------------------------------------------------------
 * File:     	function.articledate.php
 */
function smarty_function_articledate($params, &$smarty) {
    $created = $params['created'];
    $updated = $params['updated'];

    $article = isset($params['article'])? $params['article']: null;
    $starttime = strtotime($article->starttime);

    if(!empty($starttime)) {
        return '<span class="CNewsDateUpdate">'.date('d/m/Y', $starttime).' -  '.date('H:i', $starttime).' h.</span>';
    }
    
    if(preg_match('/\-/', $created)) {
        $created = strtotime($created);
    }    
    
    if( empty($updated) || preg_match('/^0000\-00\-00/', $updated) ) {
        // 11/03/09 |  01:58 h
        return '<span class="CNewsDateUpdate">'.date('d/m/Y', $created).' -  '.date('H:i', $created).' h.</span>';
    }     

    if(preg_match('/\-/', $updated)) {
        $updated = strtotime($updated);
    }
    
    // Actualizado 02/01/2009 | 00:00 h.
	return '<span class="CNewsDateUpdate">Actualizado '.date('d/m/Y', $updated).' - '.date('H:i', $updated).' h.</span>';
    
    /* if(date('YmdHi', $created) == date('YmdHi', $updated)) {
        // 11/03/09 |  01:58 h
        return( date('d/m/Y', $created).' |  '.date('H:i', $created).' h.' );
    }    
    
    if(date('Ymd', $created) == date('Ymd', $updated)) {
        // 11/03/09 |  01:58 h (actualizado ás 11:35)
        return( date('d/m/Y', $created).' |  '.date('H:i', $created).' h. (actualizado a las '.date('H:i', $updated).' h.)' );
    }
    
    //01/01/2009 | 10:10 h. (actualizado o 02/01/2009 ás 00:00 h.)
	return( date('d/m/Y', $created).' |  '.date('H:i', $created).' h. (actualizado el '.date('d/m/Y', $updated).' a las '.date('H:i', $updated).' h.)' ); */
}