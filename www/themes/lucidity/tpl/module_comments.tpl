{*
    OpenNeMas project
    @theme      Lucidity
*}
 <div class="article-comments">
    <div class="title-comments"><h3><span>{insert name="numComments" id=$content->id} Comentarios<span></h3></div>
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

    {insert name="comments" id=$content->id}

    {include file="widget_form_comments.tpl"}
</div>