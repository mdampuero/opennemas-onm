<?php
/*
 * -------------------------------------------------------------
 * File:     	function.renderLink.php
 * Check type of menu element and prepare link
 *
 */
function smarty_function_renderLink($params,&$smarty) {

    $item = $params['item'];
    $nameMenu = $params['name'];
    $nameUrl = 'seccion';
    if (isset($nameMenu) && !empty($nameMenu)) {
        if( $nameMenu == 'video') {
            $nameUrl='video';
        } elseif ($nameMenu == 'album') {
            $nameUrl='album';
        } elseif ($nameMenu == 'special') {
            $nameUrl='especiales';
        } elseif ($nameMenu == 'encuesta') {
            $nameUrl='encuesta';
        }
    }
    switch ($item->type){
        case 'category':
            $link = "/$nameUrl/$item->link/";
        break;
        case 'videoCategory':
            $link = "/video/$item->link/";
        break;
        case 'albumCategory':
            $link = "/album/$item->link/";
        break;
         case 'pollCategory':
            $link = "/encuesta/$item->link/";
        break;
        case 'static':
            $link = "/".STATIC_PAGE_PATH."/$item->link/";
        break;
        case 'internal':
             $link = "/$item->link/";
        break;
        case 'external':
             $link = "$item->link";
        break;
        case 'syncCategory':
             $link = "/ext$nameUrl/$item->link/";
        case 'blog-category':
             $link = "/blog/section/$item->link";
        break;
        default:
             $link = "/$item->link/";
        break;
    }

    return $link;

}
/*
 *
 * {*Definición de la variable 'section_url usada en menu y footer'*}
    {if preg_match('/videos\.php/',$smarty.server.SCRIPT_NAME)}
        {assign var='section_url' value='/video/'}
    {elseif preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)}
        {assign var='section_url' value='/encuesta/'}
    {elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)}
        {assign var='section_url' value='/album/'}
    {else}
        {assign var='section_url' value='/seccion/'}
    {/if}
 */