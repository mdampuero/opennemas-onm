

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}


    {/if}


{* FORMULARIO PARA ENGADIR || ACTUALIZAR *********************************** *}
	{if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}




    {/if}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$video->id}" />
</form>

{/block}
