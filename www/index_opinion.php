<?php
//Columna 2 in portada
/********************************* OPINION ***********************************/
/*
 *     {if $opinions[c]->type_opinion eq 0} Opini&oacute;n de Autor 
 *     {else}  {if $opinions[c]->type_opinion eq 1} Editorial 
 *     {else}  {if $opinions[c]->type_opinion eq 2} Carta del director {/if} {/if}
 *     {/if}
 * 
 */
/**** DIRECTOR Y EDITORIAL EN carousel.php ***************/
//$editorial = $cm->find('Opinion', 'type_opinion=1 and in_home=1 and available=1  and content_status=1', 'ORDER BY position ASC, created DESC LIMIT 0,2');
$tpl->assign('editorial', $editorial);

//$director = $cm->find('Opinion','type_opinion=2 and in_home=1 and available=1  and content_status=1', 'ORDER BY created DESC LIMIT 0,1');
$cartadirector=$director[0];
$aut = new Author($cartadirector->fk_author);
$cartadirector->name =	$aut->name;
$cartadirector->foto = $aut->get_photo($director[0]->fk_author_img)->path_img;
$tpl->assign('cartadirector', $cartadirector);

//Miramos el algoritmo de ordenacion de la opinion		

$algoritm='orden';

$order = ' position ASC, created DESC';
$opinions = $cm->find('Opinion',' opinions.type_opinion=0 and contents.in_home=1 and contents.available=1  and contents.content_status=1', 'ORDER BY '.$order.' LIMIT 0,16');

$tpl->assign('algoritm', $algoritm);

//Por posicion definida en dragdrop admin
foreach($opinions as $opinion) {

   $aut = new Author($opinion->fk_author);

   $opinion->name  =  $aut->name;
   $opinion->photo = $aut->cache->get_photo($opinion->fk_author_img_widget)->path_img;
   $opiniones[]  =  $opinion;
}				

$tpl->assign('opiniones', $opiniones);
 
$opinionVicenteMartin = $cm->find('Opinion', '`opinions`.`fk_author` = 56 AND `type_opinion` = 0 AND `available` = 1', 'ORDER BY created DESC LIMIT 0,1');
$tpl->assign('opinionVicenteMartin', $opinionVicenteMartin[0]);