{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsalbum.js" language="javascript"}

{/block}

{block name="footer-js" append}
    {script_tag src="/cropper.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="content"}

<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}

        

    {/if}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </form>
</div>

{/block}
