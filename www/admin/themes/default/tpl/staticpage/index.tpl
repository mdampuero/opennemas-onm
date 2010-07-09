{extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Static Pages Manager{/t}{/block}

{block name="head-js"}
    <script type="text/javascript" src="{$params.JS_DIR}edit_area/edit_area_full.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.localisation-min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.scrollTo-min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}ui.multiselect.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}anytime.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.dataTables.js"></script>
    
    <script type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
{/block}

{block name="head-css"}
    <link rel="stylesheet" href="{$params.CSS_DIR}ui.multiselect.css" type="text/css" />
    <link rel="stylesheet" href="{$params.CSS_DIR}anytime.css" type="text/css" />
    <link rel="stylesheet" href="{$params.CSS_DIR}datatables/style/table_jui.css" type="text/css" />
{/block}

{block name="body-content"}
    <form action="{baseurl}/{url route="staticpage-"|cat:$request->getActionName()}" method="post">

    {flashmessenger}

    {* LIST ****************************************************************** *}
    {if $request->getActionName() eq "index"}
        {include file="staticpage/list.tpl"}
    {/if}
    
    
    {* CREATE/UPDATE ********************************************************* *}
    {if ($request->getActionName() eq "create") || ($request->getActionName() eq "update")}
        {include file="staticpage/form.tpl"}
    {/if}
    
    </form>
{/block}