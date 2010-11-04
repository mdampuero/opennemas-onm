{*
    OpenNeMas project
    @theme      Lucidity
*}
{insert name="numComments" id=$content->id assign="numComments"}

<div class="article-comments">
    <div class="title-comments">
        {if $numComments gt 0}
        <h3><span>{$numComments} Comentarios<span></h3>
        {else}
        <h3><span>Sin Comentarios<span></h3>
        {/if}
    </div>
    
    <div class="utilities-comments">
        
        {if $numComments gt 0}
            {insert name="pagination_comments" total=$numComments}
        {/if}
    </div><!-- .utilities-comments -->        

    <div id="list-comments">
        {insert name="comments" id=$content->id}
    </div>

    {include file="widget_form_comments.tpl"}
</div>

{* Implements memoization javascript pattern *}
<script type="text/javascript">
/* <![CDATA[ */
var pkContent = '{$content->id}';
get_paginate_comments = function(page) {
    var url = "/comments.php?action=paginate_comments&id=" + pkContent + "&page=" + page;
    var previousContent = $("#list-comments").html();
    
    $("#list-comments").html('<img src="/themes/lucidity/images/loading.gif" border="0" />');
    
    jQuery.ajax({
        url: url,
        type: "GET",
        success: function(data) {
            $("#list-comments").html(data);
        },
        error: function() {
            $("#list-comments").html(previousContent);
        }
    });        
};

$('.utilities-comments .pagination li a').click(function(event){
    var current = $(this).attr('href');
    
    if(current) {
        $('.utilities-comments .pagination li a').each(function() {
            $(this).parent().removeClass('active');
            
            if( $(this).attr('href') == current ) {                
                $(this).parent().addClass('active');
                
                var page = $(this).attr('href').split('#')[1];
                get_paginate_comments(page);
            } 
        });                        
    }
    
    event.preventDefault();
    event.stopPropagation();
});
/* ]]> */
</script>