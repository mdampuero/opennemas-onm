{*
    OpenNeMas project
    @theme      Lucidity
*}

<div class="article-comments">
    <div class="title-comments">
        <h3><span>{insert name="numComments" id=$content->id} Comentarios<span></h3>
    </div>
    
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

    <div id="list-comments">
        {insert name="comments" id=$content->id}
    </div>

    {include file="widget_form_comments.tpl"}
</div>

{literal}
<script type="text/javascript">
get_paginate_comments = function(id, page) {
    var url = "/comments.php?action=paginate_comments&id=" + id + "&page=" + page;
    
    jQuery.ajax({
        url: url,
        type: "GET",
        success: function(data) {
            $("#list-comments").html(data);
        }
    });        
};
</script>
{/literal}