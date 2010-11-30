{extends file="base/admin.tpl"}

{block name="header-css"}
    {$smarty.block.parent}
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}dashboard.css" />
{/block}

{block name="header-js"}
    {$smarty.block.parent}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}swfobject.js"></script>
{/block}

{block name="content"}
{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "index"}
    {* ZONA MENU CATEGORIAS ******* *}
    <ul class="tabs2" style="margin-bottom: 28px;">
        <li>
            <a href="dashboard.php?action=index&category=0" id="link_global" {if $category==0} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>TODAS</a>
        </li>
          <script type="text/javascript">
                // <![CDATA[
                    {literal}
                          Event.observe($('link_global'), 'mouseover', function(event) {
                             $('menu_subcats').setOpacity(0);
                             e = setTimeout("show_subcat('{/literal}{$category}','{$home|urlencode}{literal}');$('menu_subcats').setOpacity(1);",1000);

                           });

                    {/literal}
                // ]]>
            </script>
        {include file="menu_categorys.tpl" home="dashboard.php?action=index"}
    </ul>
    <br style="clear: both;" />

    {literal}
        <div id="dashboard_enlaces">
            <a href="javascript:change_dashboard('viewed',{/literal}{$category}{literal})">Más Vistas</a> |
            <a href="javascript:change_dashboard('comented',{/literal}{$category}{literal})">Más Comentadas</a> |
            <a href="javascript:change_dashboard('voted',{/literal}{$category}{literal})">Más Valoradas</a>
        </div>
    {/literal}

    <div id="viewed">
        <table border="0" Cellpadding="0" cellspacing="0"  width="100%">
            <tr>
                <td width="33%" valign="top">
                    <div id="viewed_most_24h"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="viewed_most_48h"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="viewed_most_72h"></div>
                </td>
            </tr>
            <tr>
                <td width="33%" valign="top">
                    <div id="viewed_most_1s"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="viewed_most_2s"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="viewed_most_1m"></div>
                </td>
            </tr>
        </table>
    </div>
    <div id="comented">
        <table border="0" Cellpadding="0" cellspacing="0"  width="100%">
            <tr>
                <td width="33%" valign="top">
                    <div id="comented_most_24h"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="comented_most_48h"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="comented_most_72h"></div>
                </td>
            </tr>
            <tr>
                <td width="33%" valign="top">
                    <div id="comented_most_1s"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="comented_most_2s"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="comented_most_1m"></div>
                </td>
            </tr>
        </table>
    </div>
    <div id="voted">
        <table border="0" Cellpadding="0" cellspacing="0"  width="100%">
            <tr>
                <td width="33%" valign="top">
                    <div id="voted_most_24h"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="voted_most_48h"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="voted_most_72h"></div>
                </td>
            <tr>
                <td width="33%" valign="top">
                    <div id="voted_most_1s"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="voted_most_2s"></div>
                </td>
                <td width="33%" valign="top">
                    <div id="voted_most_1m"></div>
                </td>
            </tr>
            </tr>
        </table>
    </div>
    {literal}
    <script type="text/javascript">
    /* <![CDATA[ */
        change_dashboard('viewed',{/literal}{$category}{literal});
    /* ]]> */
    </script>
    {/literal}
{/if}

<input type="hidden" id="action" name="action" value="" /><input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
