{extends file="base/admin.tpl"}

{block name="header-js"}
    {$smarty.block.parent}
    {script_tag src="/swfobject.js" language="javascript"}
{/block}

{block name="content"}

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Content Statistics{/t}</h2></div>
    </div>
</div>

<div class="wrapper-content">

    <ul class="pills">
        <li>
            <a href="statistics.php?action=getPiwikWidgets" id="piwik" {if $category == 'piwik_widgets'} class="active"{/if}>
                {t}Piwik Widgets{/t}
            </a>
        </li>
        <li>
            <a href="statistics.php?action=index&category=0" id="link_global" {if $category == '0'} class="active"{/if}>
                {t}All Categorys{/t}
            </a>
        </li>
        {include file="menu_categories.tpl" home="statistics.php?action=index"}
    </ul>

    <div id="dashboard_enlaces">
        <a href="javascript:change_dashboard('viewed',{$category})">Más Vistas</a> |
        <a href="javascript:change_dashboard('comented',{$category})">Más Comentadas</a> |
        <a href="javascript:change_dashboard('voted',{$category})">Más Valoradas</a>
    </div>

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

    <script type="text/javascript">
    /* <![CDATA[ */
        change_dashboard('viewed',{$category});
    /* ]]> */
    </script>
</div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}
