{include file="header.tpl"
    assets_js="jquery-validate/jquery.validate.min.js"
    assets_css=""}

<form action="/admin/widget/{$request->getActionName()}" method="post" name="formulario" id="formulario">

<table class="adminform" border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
<tr>
    <td style="padding:10px;" align="left" valign="top">


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
    
<input type="hidden" id="id" name="id" value="{$id}" />    
</form>

{include file="footer.tpl"}