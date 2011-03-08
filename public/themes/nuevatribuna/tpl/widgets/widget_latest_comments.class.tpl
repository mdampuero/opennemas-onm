<div class="widget-latest-comments widget-latest-comments-wrapper clearfix">
    <div class="widget-header">
        <span>Últimos articulos comentados</span>
    </div>
    <div class="widget-content">
        <ul>
            {section name=a loop=$articles_comments max=6}
            <li {if $smarty.section.a.last}class="last"{/if}>
                <a href="{$smarty.const.SITE_URL}{$articles_comments[a]->uri}#comentarios">{$articles_comments[a]->title}</a>
            </li>
            {sectionelse}
            <li>
                En estos momentos no tenemos opiniones.
            </li>
            {/section}
        </ul>
    </div>
</div>