{* declare tag_* before header.tpl *}
{tag_script src="edit_area/edit_area_full.js"}

{tag_script src="jquery.localisation-min.js"}
{tag_script src="jquery.scrollTo-min.js"}
{tag_script src="ui.multiselect.js"}


{tag_link href="ui.multiselect.css"}


{include file="header.tpl"}

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

{include file="footer.tpl"}