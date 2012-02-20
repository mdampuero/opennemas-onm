<div data-content-id="{$content->id}" data-class="Video" class="content-provider-element clearfix">
    <div class="description">
        <input type="checkbox" class="action-button" name="selected-{$smarty.foreach.video_loop.index}">
        <div class="title">
            <span class="type">Video</span>
            {$content->title}
        </div>
    </div>
    <ul class="action-buttons">
        <li>
            <a href="" title="Eliminar"><img height=16px src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
        </li>
        <li>
            <a title="{t}Suggest to home{/t}" href="#" class="action-button home-button"></a>
        </li>
        <!-- <li>
            <a title="{t}Settings{/t}" href="#" class="action-button settings-button"></a>
        </li> -->
        <li>
            <a title="{t}Arquive this video{/t}" href="/admin/controllers/video/video.php?action=delete&amp;id={$content->id}" class="action-button delete-button"></a>
        </li>

    </ul>
    <div class="selectButton"></div>
</div>


