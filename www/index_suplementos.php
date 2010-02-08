<?php
//Columna 2 in portada

/********************************* widget suplementos ***********************************/
// FIXME: recuparar os suplementos da base de datos por algunha propiedade
$suplementos = array('contexto', 'exit','motor','nos',
                     'estratexias','esculca','libros',
                     'juridica','prensa','mirada-global');

foreach($suplementos as $sups) {
    //$titulares = $cm->find_by_category_name('Article', $sups, 'contents.content_status=1 AND contents.frontpage=1 AND available=1 AND fk_content_type=1', 'ORDER BY position ASC LIMIT 0 , 1');
    $sups_id = $ccm->get_id($sups);
    $titulares = $cm->find_category_headline($sups_id, 'contents.available=1 AND contents.content_status=1 AND contents.frontpage=1',
                                             'ORDER BY position ASC LIMIT 0 , 1');
    
    // FIXME: correxir o problema, non poñer parches
    if($sups == 'mirada-global') {
        $sups = 'mirada';
    }
    
    $name = 'titulares_'.$sups;
    
    //Uncomment these lines for picking the random headlines
    //Have a look in limit in find_by_category_name (limit 0,10)
    //$result = count($titulares);
    //$r=rand(0,--$result);
    //$tpl->assign($name, $titulares[$r]);

    $tpl->assign($name, $titulares[0]);
}