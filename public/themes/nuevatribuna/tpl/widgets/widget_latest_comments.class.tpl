<div class="widget-latest-comments widget-latest-comments-wrapper clearfix">
    <div class="widget-header">
        <img src="{$params.IMAGE_DIR}/logos/nuevatribuna-square.png" alt="Opiniones Nueva Tribuna" /> 
        <span>Últimos articulos comentados</span>
    </div>
    <div class="widget-content">
        <ul>
            {section name=a loop=$articles_comments max=6}
            <li {if $smarty.section.a.last}class="last"{/if}>
                <h5><a href="{$smarty.const.SITE_URL}{$articles_comments[a]->uri}#comentarios">{$articles_comments[a]->title}</a></h5>
                <h7>{$articles_comments[a]->comment}</h7>
            </li>
            {sectionelse}
            <li>
                No hay se ha realizado ningún comentario ultimamente. Sea el primero! Vaya a una noticia y deje su opinion.
            </li>
            {/section}
        </ul>
    </div>  
</div>

