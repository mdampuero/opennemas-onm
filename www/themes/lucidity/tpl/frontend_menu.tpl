{*
    OpenNeMas project

    @theme      Lucidity
*}
    
<div id="menus" class="">

    <div id="main_menu" class="span-24 clearfix">
        <div>
            <ul class="clearfix">
                <li><a href="/" title="Portada">PORTADA</a></li>
                {foreach key=k item=v from=$categories}
                    <li><a href="/seccion/{$v.name}/" title="Sección: {$v.title}">{$v.title}</a></li>
                {/foreach}
            </ul>
        </div>

    </div>

    <div id="sub_main_menu" class="span-24">
        {if !empty($categories[$posmenu].subcategories)}
            <ul class="clearfix">
                {foreach key=k item=v from=$categories[$posmenu].subcategories}
                    <li><a href="/seccion/{$categories[$posmenu].name}/{$k}/" title="{$v}">{$v}</a></li>
                {/foreach}
            </ul>
        {/if}
        {include file="widget_search.tpl" }

    </div>

</div>

 