{*
    OpenNeMas project
    @theme      Lucidity
*}
 <div class="article-comments">
    <div class="title-comments"><h3><span>{insert name="numComments" id=$article->id} Comentarios<span></h3></div>
    <div class="utilities-comments">
            <div class="num-pages span-6">Página 1 de {$paginacion->links}</div>
            <div class="span-9 pagination">
                    <ul>
                            <li class="active"><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                    ...
                            <li><a href="#">9</a></li>
                            <li class="next"><a href="#">Siguiente</a></li>
                    </ul>
            </div>
    </div><!-- .utilities-comments -->

    {if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
        {insert name="comments" id=$opinion->id}
    {elseif preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) || (preg_match('/preview_content\.php/',$smarty.server.SCRIPT_NAME)||($smarty.request.action eq "article"))}
        {insert name="comments" id=$article->id}
    {/if}
    {include file="widget_form_comments.tpl"}
</div>