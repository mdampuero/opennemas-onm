{include file="header.tpl"}

{* $request->getControllerName() *}

<form action="{baseurl}/category/{$request->getActionName()}" method="post">

<table class="adminform" border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
<tr><td style="padding:10px;" align="left" valign="top">

{flashmessenger}

{* LISTADO ******************************************************************* *}
{if $request->getActionName() eq "index"}        
    {include file="category/list.tpl"}
{/if}


{* FORMULARIO PARA ENGADIR OU MODIFICAR  ************************************** *}
{if ($request->getActionName() eq "create") || ($request->getActionName() eq "update")}
    {include file="category/form.tpl"}
{/if}

</td></tr>
</table>
    
</form>

{include file="footer.tpl"}