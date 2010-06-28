{extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Widget Manager{/t}{/block}

{block name="head-js"}
    <script type="text/javascript" src="{$params.JS_DIR}edit_area/edit_area_full.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.localisation-min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.scrollTo-min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}ui.multiselect.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}anytime.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.dataTables.js"></script>
{/block}

{block name="head-css"}
    <link rel="stylesheet" href="{$params.CSS_DIR}ui.multiselect.css" type="text/css" />
    <link rel="stylesheet" href="{$params.CSS_DIR}anytime.css" type="text/css" />
    <link rel="stylesheet" href="{$params.CSS_DIR}datatables/style/table_jui.css" type="text/css" />
{/block}

{block name="body-content"}
    <form action="{baseurl}/widget/{$request->getActionName()}" method="post" name="formulario" id="formulario">

    <table class="adminform" border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
    <tr><td style="padding:10px;" align="left" valign="top">

    {flashmessenger}

    {* LISTADO ******************************************************************* *}
    {if $request->getActionName() eq "index"}
        {include file="widget/list.tpl"}
    {/if}
    
    
    {* FORMULARIO PARA ENGADIR OU MODIFICAR  ************************************** *}
    {if ($request->getActionName() eq "create") || ($request->getActionName() eq "update")}
        {include file="widget/form.tpl"}
    {/if}
    
    </td></tr>
    </table>
        
    </form>
{/block}