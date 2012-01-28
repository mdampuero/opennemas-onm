<ul id='thelist' class="gallery_list clearfix" style="width:100%; margin:0; padding:0">
   {assign var=num value='1'}
   {section name=n loop=$videos}

   {if $videos[n]->content_status eq 1}
        <!-- {$videos[n]|var_dump} -->
       <li style="display:inline-block;">
           <a>
                <img width="67"
                    style="width: 66px;"
                    class="draggable-handler"
                    src="{$videos[n]->thumb}"
                    alt="{$videos[n]->title}"
                    data-id="{$videos[n]->pk_video}"
                    data-url="{$videos[n]->thumb}"
                    data-filename="{$videos[n]->video_url}"
                    data-created="{$videos[n]->created}"
                    data-description="{$videos[n]->title}"
                    data-tags="{$videos[n]->metadata}"
                />
            </a>
       </li>
       {assign var=num value=$num+1}
    {/if}
    {/section}
</ul>
{if $videoPager}
    <div class="pagination" align="center" style="clear:both; width:100%"> {$pager} </div>
{/if}
<script>
jQuery(document).ready(function($){
    $( "#videos-container #videos .draggable-handler").draggable({ opacity: 0.7, helper: "clone"});
    $( ".droppable-video-position" ).droppable({
        accept: "#videos-container #videos .draggable-handler",
        drop: function( event, ui ) {
            var image = ui.draggable;
            var parent = $(this);

            // Change the image thumbnail to the new one
            parent.find('.thumbnail img').attr("src", image.data("url"));
            // Change the image information to the new one
            var article_info = parent.find(".article-resource-image-info");
            article_info.find(".filename").html(image.data("filename"));
            article_info.find(".image_size").html(image.data("width") + " x "+ image.data("height") + " px");
            article_info.find(".file_size").html(image.data("weight") + " Kb");
            article_info.find(".created_time").html(image.data("created"));
            article_info.find(".description").html(image.data("description"));
            article_info.find(".tags").html(image.data("tags"));
            // Change the form values
            var article_inputs = parent.find(".article-resource-footer");
            article_inputs.find("input[type='hidden']").attr('value', image.data("id"));
            article_inputs.find("textarea").attr('value', image.data("description"));
        }
    });
});
</script>