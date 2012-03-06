{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/swfobject.js" language="javascript"}
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery("#statistics-tabbed").tabs();
        });
    </script>
{/block}

{block name="header-css" append}
<style type="text/css">
.piwikWidget iframe {
    width:100%;
    height:630px;
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
            <a href="statistics.php?action=index&category=0" id="link_global" {if $category== '0'}class="active"{/if}>
                {t}All Categorys{/t}
            </a>
        </li>
        {include file="menu_categories.tpl" home="statistics.php?action=index"}
    </ul>

    <div id="statistics-tabbed" class="tabs">
        <ul>
            <li><a href="#last-visits">{t}General{/t}</a></li>
            <li><a href="#browsers">{t}Visitors Browsers{/t}</a></li>
            <li><a href="#search-engines">{t}Search Engines{/t}</a></li>
            <li><a href="#page-titles">{t}Page Titles{/t}</a></li>
            <li><a href="#external-websites">{t}External Websites{/t}</a></li>
            <li><a href="#keywords">{t}Keywords List{/t}</a></li>
        </ul>


        <div id="last-visits" class="piwikWidget">
            <iframe src="{$last_visits}" scrolling="no" frameborder="0" marginheight="0" marginwidth="0">
            </iframe>
        </div>
        <div id="browsers" class="piwikWidget">
            <iframe src="{$visitors_browsers}" scrolling="no" frameborder="0" marginheight="0" marginwidth="0">
            </iframe>
        </div>
        <div id="search-engines" class="piwikWidget">
            <iframe src="{$best_search_engines}" scrolling="no" frameborder="0" marginheight="0" marginwidth="0">
            </iframe>
        </div>
        <div id="page-titles" class="piwikWidget">
            <iframe src="{$page_titles}" scrolling="no" frameborder="0" marginheight="0" marginwidth="0">
            </iframe>
        </div>
        <div id="external-websites" class="piwikWidget">
            <iframe src="{$list_keywords}" scrolling="no" frameborder="0" marginheight="0" marginwidth="0">
            </iframe>
        </div>
        <div id="keywords" class="piwikWidget">
            <iframe src="{$external_websites}" scrolling="no" frameborder="0" marginheight="0" marginwidth="0">
            </iframe>
        </div>
    </div>
</div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
{/block}