{include file="widget/header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
    {include file="widget/list.tpl"}
{/if}


{* FORMULARIO PARA ENGADIR OU MODIFICAR  ************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}
    {include file="widget/form.tpl"}
{/if}

{include file="widget/footer.tpl"}
