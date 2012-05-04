{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    #viewed > div, #comented > div, #voted > div {
        width:32%;
        display:inline-block;
        vertical-align: top;
        margin-right:2px;
        padding:3px;
    }
    #viewed > div table, #comented > div table, #voted > div table {
        width:97%;
    }
</style>
{/block}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery.min.js"}
    <script>
    jQuery(document).ready(function() {
        jQuery("#statistics").tabs();
    });
    </script>
    {script_tag src="/swfobject.js"}

    <script type="text/javascript">

    jQuery(document).ready(function($){
        $('#viewed, #comented, #voted').each(function(el){
            var url = $(this).data('url');
            $(this).children('div').each(function(){
                var el = $(this);
                var days = $(this).data('days');
                var url_complete = url +   days;
                $.ajax({
                    url: url_complete,
                    success: function(text) {
                        el.html(text);
                        // $(this).html(text);
                    }
                })
            });
        })
    });
</script>
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
            <a href="{url name="admin_statistics"}?category=0" {if $category == '0'} class="active"{/if}>
                {t}All Categorys{/t}
            </a>
        </li>
        {include file="menu_categories.tpl" home="{url name="admin_statistics"}?ext=1"}
    </ul>

    <div id="statistics" class="tabs">
        <ul>
            <li><a href="#viewed">{t}More viewed{/t}</a></li>
            <li><a href="#comented">{t}More commented{/t}</a></li>
            <li><a href="#voted">{t}More voted{/t}</a></li>
        </ul>
        <div id="viewed" data-url="{url name=admin_statistics_widget}?type=viewed&amp;category={$category}&amp;days=">
            <div id="viewed_most_24h" data-days="1">{t}Loading...{/t}</div>
            <div id="viewed_most_48h" data-days="2">{t}Loading...{/t}</div>
            <div id="viewed_most_72h" data-days="3">{t}Loading...{/t}</div>
            <div id="viewed_most_1s"  data-days="7">{t}Loading...{/t}</div>
            <div id="viewed_most_2s"  data-days="14">{t}Loading...{/t}</div>
            <div id="viewed_most_1m"  data-days="30">{t}Loading...{/t}</div>
        </div>
        <div id="comented" data-url="{url name=admin_statistics_widget}?type=comented&amp;category={$category}&amp;days=">
            <div id="comented_most_24h" data-days="1">{t}Loading...{/t}</div>
            <div id="comented_most_48h" data-days="2">{t}Loading...{/t}</div>
            <div id="comented_most_72h" data-days="3">{t}Loading...{/t}</div>
            <div id="comented_most_1s"  data-days="7">{t}Loading...{/t}</div>
            <div id="comented_most_2s"  data-days="14">{t}Loading...{/t}</div>
            <div id="comented_most_1m"  data-days="30">{t}Loading...{/t}</div>
        </div>
        <div id="voted" data-url="{url name=admin_statistics_widget}?type=voted&amp;category={$category}&amp;days=">
            <div id="voted_most_24h" data-days="1">{t}Loading...{/t}</div>
            <div id="voted_most_48h" data-days="2">{t}Loading...{/t}</div>
            <div id="voted_most_72h" data-days="3">{t}Loading...{/t}</div>
            <div id="voted_most_1s"  data-days="7">{t}Loading...{/t}</div>
            <div id="voted_most_2s"  data-days="14">{t}Loading...{/t}</div>
            <div id="voted_most_1m"  data-days="30">{t}Loading...{/t}</div>
        </div>
    </div>
</div>
{/block}
