{*
    OpenNeMas project
    @theme      Lucidity
*}
{if preg_match('/videos\.php/',$smarty.server.SCRIPT_NAME)} {assign var='section_url' value='/video/'} {elseif preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)} {assign var='section_url' value='/encuesta/'} {elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)}{assign var='section_url' value='/album/'}{else}{assign var='section_url' value='/seccion/'} {/if}
<a href="#main_content" class="jump-to-content">Saltar al contenido</a>
<div id="menu">
    <div id="submenu">
        <ul class="clearfix">
             <li class="cat home{if $category_name eq "home" || $category_name eq "opinion" || $section_url eq "video"} active{/if}">
                <a href="/" title="Portada">Portada</a>
                <ul class="nav{if $subcategory_name eq $subcat_key} active{/if}">
                    <li class="subcat {if $subcategory_name eq 'opinion' || $category_name eq 'opinion'} active{/if}"><a title="Ver la sección de Opinion" href="/seccion/opinion/">Opinión</a></li>
                    <!--<li class="subcat {if $subcategory_name eq 'videos' || $section_url eq "video"} active{/if}"><a title="Ver la sección de Vídeos" href="/video/">Vídeos</a></li>-->
                    <!--<li class="subcat {if $subcategory_name eq 'album' || $section_url eq "album"  || preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)} active{/if}"><a title="Ver la sección de galerías" href="/albumes/">Galerías</a></li>-->
                    <!--<li class="subcat {if preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)  || $category_name eq 'encuesta'} active{/if}"><a title="Ver  Encuestas" href="/encuesta/deportes/">Encuestas</a></li>-->
                    <li class="subcat {if $subcategory_name eq 'mobile'} active{/if}"><a title="Ver la versión de móvil" href="/mobile/">Versión móvil</a></li>
                </ul>
            </li>
            {foreach key=category_key item=category_value from=$categories}
            <li class="cat {$category_value.name}{if $category_name eq $category_value.name} active{/if}">

                <a href="{$section_url}{$category_value.name}/" title="Sección: {$category_value.title}">{$category_value.title|mb_lower}</a>
                {if count($category_value.subcategories) > 0}
                <ul class="nav">
                {foreach key=subcat_key item=subcat_value from=$category_value.subcategories}
                {if ($section_url eq '/seccion/') && ($subcat_value.internal_category neq 1)}
                {else}
                    <li class="subcat {if $subcategory_name eq $subcat_key}active{/if}"><a href="{$section_url}{$category_value.name}/{$subcat_key}/" title="{$subcat_value.title|mb_lower}">{$subcat_value.title|mb_lower}</a></li>
                {/if}
                {/foreach}
                </ul>
                {/if}
            </li>
            {/foreach}
        </ul>

    </div>

    <div id="social-icons-submenu">
        <ul>
            <li><a href="http://twitter.com/nuevatribuna" title="Visita nuestro perfíl en Twitter"><img src="{$params.IMAGE_DIR}/bullets/twitter_32.png" alt="" /></a></li>
            <li><a href="http://www.facebook.com/pages/nuevatribunaes/370028714939" title="Visita nuestro perfíl en Facebook"><img src="{$params.IMAGE_DIR}/bullets/facebook_32.png" alt="" /></a></li>
            <li><a href="/rss/listado" title="Siga las noticias con nuestro RSS"><img src="{$params.IMAGE_DIR}/bullets/feed_32.png" alt="" /></a></li>
        </ul>
    </div>
    <div id="main-search-form">
        <form action="/search/google/">
            <input type="text" name="q" value="{$smarty.get.q|default:'Buscar...'}"
                   onblur="if(this.value=='') this.value='Buscar...';"
                   onfocus="if(this.value=='Buscar...') this.value='';" />
            <input type="submit" name="lastname" value="Buscar" />

            <input type="hidden" name="cx" value="015025374634274414484:ykq8y_ayqku" />
            <input type="hidden" name="cof" value="FORID:10" />
            <input type="hidden" name="ie" value="UTF-8" />
            <script type="text/javascript" defer="defer" src="http://www.google.es/coop/cse/brand?form=cse-search-box&lang=es"></script>
        </form>
    </div>
</div>

<script type="text/javascript" src="{$params.JS_DIR}menu/menu.js"></script>
