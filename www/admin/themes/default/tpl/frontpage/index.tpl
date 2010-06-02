{* liquid.css, blueprint css plugin: http://www.ixda.org/node/15655 *}
{tag_link rel="stylesheet" type="text/css" href="blueprint/grid.css"}
{tag_link rel="stylesheet" type="text/css" href="blueprint/liquid.css"}

{* http://www.filamentgroup.com/lab/jquery_ui_selectmenu_an_aria_accessible_plugin_for_styling_a_html_select/ *}
{tag_script src="ui.selectmenu.js"}
{tag_link rel="stylesheet" type="text/css" href="ui.selectmenu.css"}


{include file="header.tpl"}

<form action="{baseurl}/frontpage/{$request->getActionName()}" method="POST">

<table class="adminform" border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
<tr><td style="padding:10px;" align="left" valign="top">

{flashmessenger}

{if ($request->getActionName() eq "edit")}
    {include file="frontpage/board.tpl"}
{/if}

</td></tr>
</table>
    
</form>

{include file="footer.tpl"}