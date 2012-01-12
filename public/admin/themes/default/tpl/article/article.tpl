{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsarticle.js" language="javascript"}
    {script_tag src="/editables.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="content"}
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
        <div id="content-wrapper">
        {if isset($smarty.request.action) && $smarty.request.action eq "list_agency"}
            {include  file="article/agencys.tpl"}
        {/if}

        {if isset($smarty.request.action) && $smarty.request.action eq "list_hemeroteca"}
            {include  file="article/library.tpl"}
        {/if}

        {if isset($smarty.request.action) && $smarty.request.action eq "only_read"}
            {include  file="article/only_read.tpl"}
        {/if}
        </div>
    </form>
</div>
{/block}
