{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsalbum.js"></script>

{/block}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}cropper.js"></script>
     <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsGallery.js"></script>
{/block}

{block name="content"}

<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}

        

    {/if}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
    </form>
</div>

{/block}
