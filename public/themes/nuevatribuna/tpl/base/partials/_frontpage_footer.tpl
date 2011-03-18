{*
    OpenNeMas project

    @theme      Lucidity
*}

    <div class="wrapper_content_inside_container">
        <div class="span-24">
            <div class="footer-categories clearfix">
                <ul class="span-17">
                    {foreach key=category_key item=category_value from=$categories}
                    <li class="{$category_value.name}{if $category_name eq $category_value.name} active{/if} clearfix">
                        <a href="{$section_url}{$category_value.name}/"
                           title="Sección: {$category_value.title}">{$category_value.title|mb_lower}</a>
                    </li>
                    {/foreach}
                    <li class="last"><a class="jump-to-up" href="#main_content">Volver arriba</a></li>
                </ul>
            </div>
            <div class="main-static-pages last">
                <ul >
                 {section name=k loop=$statics}
                    {if $statics[k]->title eq 'Suscripción'}
                        <li><a href="{$smarty.const.SITE_URL}controllers/subscripcionBoletin.php">{$statics[k]->title}</a></li>
                    {else}
                        <li><a href="/{$smarty.const.STATIC_PAGE_PATH}{$statics[k]->slug}.html">{$statics[k]->title}</a></li>
                    {/if}
                 {/section}
                </ul>
            </div>
        </div>
        <div>
            <div class="span-16 contact-data">
                <img src="{$params.IMAGE_DIR}/logos/nuevatribuna-footer.png"  alt="Nueva tribuna"/>
                <div class="info">
                    &copy; medio digital de información general<br/>
                    editado por <strong>Página 7 Comunicación S.L.</strong><br/>
                    C/ Noblejas, 5 Bajo - 28013 Madrid
                </div>
            </div>
     
            <div class="span-8 last developed-by">
                Desarrollado por OpenHost con: &nbsp; &nbsp; &nbsp;<br/>

                <img src ="{$params.IMAGE_DIR}logos/logo-onm-small.png" usemap ="#ohmap" />

                <map name="ohmap">
                  <area shape="rect" coords="0,0,50,50" href="http://www.openhost.es/" alt="OpenHost S.L." />
                  <area shape="rect" coords="60,10,195,97" href="http://www.openhost.es/es/opennemas" alt="Opennemas CMS" />
                </map>


<!--                <a style="float:left" href="http://www.openhost.es/" title="OpenHost S.L.">
                    <img src="{$params.IMAGE_DIR}logos/logo-oh-small.png" alt="OpenNemas Framework" />
                </a>
                <a style="float:right" href="http://www.opennemas.com/" title="Opennemas CMS">
                    <img src="{$params.IMAGE_DIR}logos/logo-onnemas-small.png" alt="OpenNemas Framework" />
                </a>-->
            </div>
    
        </div>
    </div>
