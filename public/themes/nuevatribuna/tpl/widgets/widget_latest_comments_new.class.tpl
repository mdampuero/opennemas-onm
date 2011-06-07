<div class="widget-latest-comments widget-latest-comments-wrapper clearfix">
    <script type="text/javascript" src="{$params.JS_DIR}wz_tooltip.js"></script>
    <div class="widget-header">
        <img src="{$params.IMAGE_DIR}/logos/nuevatribuna-square.png" alt="Opiniones Nueva Tribuna" /> 
        <span>Últimos comentarios de lectores</span>
    </div>
    <div class="widget-content">
        <ul>
            {section name=a loop=$articles_comments max=6}
            <li {if $smarty.section.a.last}class="last"{/if}>
                <strong>{$articles_comments[a]->comment_title}</strong> ({$articles_comments[a]->comment_author}):
                <a style="font-size:0.95em" onmouseout="UnTip()" 
                   onmouseover="Tip('<b>Comentario:</b> {$articles_comments[a]->comment|escape:quotes|escape:javascript|escape:htmlall|clearslash}',BGCOLOR,'#A14646',FONTCOLOR,'#FFFFFF',BORDERCOLOR,'#A14646', SHADOW, false, ABOVE, true, WIDTH, 400)" 
                   href="{$smarty.const.SITE_URL}{$articles_comments[a]->uri}#{$articles_comments[a]->pk_comment}" target="_blank" title="Ir al comentario" >{$articles_comments[a]->comment|truncate:150:"..."}</a>
                </a>
            </li>
            {sectionelse}
            <li>
                No hay se ha realizado ningún comentario ultimamente. Sea el primero! Vaya a una noticia y deje su opinion.
            </li>
            {/section}
        </ul>
    </div>  
</div>

