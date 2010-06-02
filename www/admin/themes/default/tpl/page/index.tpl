{* Categories pane *}
{tag_script src="jquery.localisation-min.js"}
{tag_script src="jquery.scrollTo-min.js"}
{tag_script src="ui.multiselect.js"}
{tag_link href="ui.multiselect.css"}

{* Select themes *}
{tag_script src="ui.selectmenu.js"}
{tag_link href="ui.selectmenu.css"}

{* Datetime picker *}
{tag_script src="anytime.js"}
{tag_link href="anytime.css"}


{include file="header.tpl"}

<form action="{baseurl}/page/{$request->getActionName()}/" method="post" name="formulario" id="formulario">

<table class="adminform" border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
<tr><td style="padding:10px;" align="left" valign="top">

{flashmessenger}

{if $request->getActionName() == 'index'}
    {include file="page/list.tpl"}
{/if}

{if $request->getActionName() == 'create' || $request->getActionName() == 'update'}
    {include file="page/form.tpl"}
{/if}

</td></tr>
</tbody>
</table>

</form>

{include file="footer.tpl"}