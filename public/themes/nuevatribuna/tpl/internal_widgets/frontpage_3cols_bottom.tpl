{*
    OpenNeMas project

    @theme      Lucidity
*}
{if $category_name eq 'home'}
    <div style="float:left; display:block; margin-left:-10px; width:200px;height:200px;">
        {insert name="renderbanner" type=16 width="200" height="200" cssclass=""}
    </div>
    <div class="layout-column last" style="float:left; display:block; margin-left:9px; width:573px;">
        <div id="c_1b0ecb1ce98a28450855ef35a639c1a4" class="completo">
            <h2 style="color: #000000; margin: 0 0 3px; padding: 2px; font: bold 13px/1.2 Verdana; text-align: center;">
            <a href="http://www.eltiempo.es/" style="color: #000000; text-decoration: none;">tiempo</a> en Madrid</h2>
        </div>
        <script type="text/javascript" src="http://www.eltiempo.es/widget/widget_loader/1b0ecb1ce98a28450855ef35a639c1a4"></script>
    </div>
    <div style="float:left; display:block; margin-left:10px; margin-right:-10px; width:200px;height:200px;">
        {insert name="renderbanner" type=36 width="200" height="200"  cssclass=""}
    </div>
{else}
    {include file="internal_widgets/frontpage_headlines_all_categories.tpl"}
{/if}