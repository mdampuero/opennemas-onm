{* ### Ticker de infobolsa ################################################### *}
{if strtolower($category_name) == 'economia'}
<div style="text-align: center;">
    <iframe id="ticker" src="http://www.infobolsa.es/web/Default.aspx?PageID=181&width=970&speed=2" width="970" height="18"
        scrolling="no" frameborder="0" noresize="noresize"></iframe>
</div>            
{/if}

{if !empty($categories[$posmenu].subcategories)}
    <div class="zonaHoraBusqueda zonaHoraBusquedaSec">
        <div class="zonaHoraFecha">
            <div class="time-box">
                {include file="modulo_categoriesMenu.tpl"}
            </div>          
            <div class="rss-box">
                <a href="/rss/{if (!empty($category_name) && $category_name!="home")}{$category_name}/{/if}{if !empty($subcategory_name)}{$subcategory_name}/{/if}"><img src="{$params.IMAGE_DIR}rss-icon.gif" alt="RSS" /></a>
                
                {if ($category_name!='home') && !isset($author_name)}
                    <a href="/rss/{$category_name}/{if !empty($subcategory_name)}{$subcategory_name}/{/if}">
                        &nbsp;{$subcategory_real_name|default:$category_real_name}&nbsp;</a>
                {elseif isset($author_name)}
                    <a href="/rss/{$category_name}/{$author_id}/">&nbsp;{$author_name}&nbsp;</a>
                {/if}
            </div>
        </div>
        <div class="zonaBusquedaBarraHora zonaBusquedaBarraHoraSec">
	    <div style="float:right" class="containerBusqueda">
            <form action="/search.php" id="cse-search-box">
                <input type="hidden" name="cx" value="partner-pub-4524925515449269:kfaqom-99at" />
                <input type="hidden" name="cof" value="FORID:10" />
                <input type="hidden" name="ie" value="UTF-8" />
                <div class="elemMenuBarraFecha">Buscar en:</div>
                <div class="cajaBusqueda"><input class="textoABuscar" name="q" type="text" /></div>
                <div class="destinoBusqueda">
                    <div class="radioBusqueda"><input type="radio" name="destino" value="xornal" checked="checked" onclick="cx.value='partner-pub-4524925515449269:kfaqom-99at'" /></div>
                    <div class="dondeBuscar">Xornal</div>
                </div>
                <div class="destinoBusqueda">
                    <div class="radioBusqueda"><input type="radio" name="destino" value="google" onclick="cx.value='partner-pub-4524925515449269:l5xds69cix0'" /></div>
                    <div class="dondeBuscar">Google&nbsp;</div>
                </div>
                <script type="text/javascript" src=" http://www.google.es/coop/cse/brand?form=cse-search-box&lang=es"></script>
            </form>
	    </div>
        </div>
    </div>
{else}
    <div class="zonaHoraBusqueda">
        <div class="zonaHoraFecha">
            <div class="time-box">
                {insert name="time"}
            </div>
            {* <a href="/rss/{if (!empty($category_name) && $category_name!="home")}{$category_name}/{/if}{if isset($smarty.request.author_id)}{$smarty.request.author_id}/{/if}"><img src="{$params.IMAGE_DIR}rss.gif" alt="RSS" id="rss" /></a> *}
            
            {if $category_name != 'kiosko'}
                <div class="rss-box">
                    <a href="/rss/{if (!empty($category_name) && $category_name!="home")}{$category_name}/{/if}{if !empty($subcategory_name)}{$subcategory_name}/{/if}"><img src="{$params.IMAGE_DIR}rss-icon.gif" alt="RSS" /></a>

                    {if ($category_name!='home') && !isset($author_name)}
                        <a href="/rss/{$category_name}/{if !empty($subcategory_name)}{$subcategory_name}/{/if}">
                            &nbsp;{$subcategory_real_name|default:$category_real_name}&nbsp;</a>
                    {elseif isset($author_name)}
                        <a href="/rss/{$category_name}/{$author_id}/">&nbsp;{$author_name}&nbsp;</a>
                    {/if}
                </div>
            {/if}
            {if !is_null($frontpage_newspaper_pdf)}
            <div class="box-portada">
                <strong><a href="{$smarty.const.SITE_URL}media/files/kiosko/{$frontpage_newspaper_pdf}" target="_blank" title="Primera página de la versión impresa">Portada de la versión impresa</a></strong>
            </div>
            {/if}                
        </div>
        <div class="zonaBusquedaBarraHora" style="width: 440px;">
            <div style="float:right" class="containerBusqueda">
                <form action="/search.php" id="cse-search-box">
                    <input type="hidden" name="cx" value="partner-pub-4524925515449269:kfaqom-99at" />
                    <input type="hidden" name="cof" value="FORID:10" />
                    <input type="hidden" name="ie" value="UTF-8" />
                    <div class="elemMenuBarraFecha">Buscar en:</div>
                    <div class="cajaBusqueda"><input class="textoABuscar" name="q" type="text" /></div>
                    <div class="destinoBusqueda">
                        <div class="radioBusqueda"><input type="radio" name="destino" value="xornal" checked="checked" onclick="cx.value='partner-pub-4524925515449269:kfaqom-99at'" /></div>
                        <div class="dondeBuscar">Xornal</div>
                    </div>
                    <div class="destinoBusqueda">
                        <div class="radioBusqueda"><input type="radio" name="destino" value="google" onclick="cx.value='partner-pub-4524925515449269:l5xds69cix0'" /></div>
                        <div class="dondeBuscar">Google&nbsp;</div>
                    </div>
                </form>
            </div>
        </div>  
    </div>
{/if}