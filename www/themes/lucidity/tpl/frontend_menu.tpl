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
                    <li {if $category_name eq $v.name} class="active"{/if}><a href="/seccion/{$v.name}/" title="Sección: {$v.title}">{$v.title}</a></li>
                {/foreach}
            </ul>
        </div>

    </div>

    <div id="sub_main_menu" class="span-24">
        {if !empty($categories[$posmenu].subcategories)}
            <ul class="clearfix">
                {foreach key=s item=v from=$categories[$posmenu].subcategories}
                    <li {if $subcategory_name eq $s} class="active"{/if}><a href="/seccion/{$categories[$posmenu].name}/{$s}/" title="{$v}">{$v}</a></li>
                {/foreach}
            </ul>
        {/if}
        {include file="widget_search.tpl" }

    </div>

</div>

 