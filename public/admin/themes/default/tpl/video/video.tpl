

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}


    {/if}


{* FORMULARIO PARA ENGADIR || ACTUALIZAR *********************************** *}
	{if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}


    {/if}


{/block}
