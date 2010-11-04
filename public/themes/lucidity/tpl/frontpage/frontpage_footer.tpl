{*
    OpenNeMas project

    @theme      Lucidity
*}
   
    <hr class="new-separator" />
    <div class="span-24">
        <div class="span-17">
            <ul class="public-footer">
             {section name=k loop=$statics}
                <li><a href="/{$smarty.const.STATIC_PAGE_PATH}{$statics[k]->slug}.html">{$statics[k]->title}</a></li>
             {/section}
            </ul>
        </div>
        <div class="span-7 last">
            <ul>
                <li><a href="http://www.facebook.com/home.php?#!/pages/OpenNemas/282535299100"><img src="{$params.IMAGE_DIR}facebook-footer.png" alt="OpenNemas Framework" /> OpenNemas en Facebook</li>
                <li class="last"><a href="/rss/"><img src="{$params.IMAGE_DIR}rss-sindication-nocolor.png" alt="OpenNemas Framework" /> Subscripción RSS</a></li>
            </ul>
        </div>
    </div>
    <hr class="new-separator" />
    <div>
        <div class="span-12 contact-dates">
            &copy; <strong>Diario Retrincos Times</strong><br/>
            Progreso, 64 4A<br/>
            32003, Ourense (Spain)<br/>
            +34 655172329<br/>
            OpenHost Media Press S.L.
        </div>
 
        <div class="span-12 last developed-by">
            Desarrollado por:<br/>
            <a href="http://www.openhost.es/" title="OpenHost S.L.">
                <img src="{$params.IMAGE_DIR}logo-onm-small.png" alt="OpenNemas Framework" />
            </a>
        </div>

    </div>
