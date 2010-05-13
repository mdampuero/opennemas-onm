{* Define link_javascript before blocks scriptsection *}
{tag_script src="edit_area/edit_area_full.js" section="head"}

{include file="header.tpl"}

<form action="{baseurl}/widget/{$request->getActionName()}" method="post" name="formulario" id="formulario">

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

<script type="text/javascript">
console.log("Action: {$request->getActionName()}");
</script>
    
</form>

{include file="footer.tpl"}