{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/swfobject.js" language="javascript"}
{/block}

{block name="header-css" append}
<style type="text/css">
.piwikWidget iframe {
    width:100%;
    height:500px;
}
.piwikWidgetDoble iframe {
    width:50%;
    height:500px;
    float:left;
}
</style>
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
            <a href="statistics.php?action=getPiwikMost" id="piwik_visits" {if $category == 'piwik_visits'} class="active"{/if}>
                {t}Piwik Statistics{/t}
            </a>
        </li>
        <li>
            <a href="statistics.php?action=index&category=0" id="link_global" {if $category== '0'}class="active"{/if}>
                {t}All Categorys{/t}
            </a>
        </li>
        {include file="menu_categories.tpl" home="statistics.php?action=index"}
    </ul>


    <div id="widgetIframe">
        <iframe style="width:100%;height:500px;" src="{$most_viewed}" scrolling="no" frameborder="0" marginheight="0" marginwidth="0">
        </iframe>
    </div>

</div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}