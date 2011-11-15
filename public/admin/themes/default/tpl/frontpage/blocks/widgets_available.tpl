<div id="widgets_available" class="content-provider-block">
    {foreach from=$widgets item=content name=widget_loop}
    <div data-id="{$content->id}" data-class="Widget" class="content-provider-element clearfix">
        <div class="description">
            <input type="checkbox" class="action-button" name="selected-{$smarty.foreach.widget_loop.index}">
            <div class="title">
                <span class="type">Widget</span>
                {$content->title}
            </div>
        </div>
        <ul class="action-buttons">
            <li>
                <a href="" title="Eliminar"><img height=16px src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
            </li>
            {if ($widgets[d]->renderlet != 'intelligentwidget')}
            <li>
                <a title="{t}Edit{/t} '{$content->title}'" href="/admin/controllers/widget/widget.php?action=edit&id={$widget->pk_widget}&category={$smarty.request.category}" class="action-button edit-button"></a>
            </li>
            {/if}
            <li>
                <a title="{t}Suggest to home{/t}" href="#" class="action-button home-button"></a>
            </li>
            
            <li>
                <a title="{t}Settings{/t}" href="#" class="action-button settings-button"></a>
            </li>
            <li>
                <a title="{t}Arquive this widget{/t}" href="/admin/controllers/widget/widget.php?action=delete&id={$widgets[d]->pk_widget}" class="action-button delete-button"></a>
            </li>
            
        </ul>   
        <div class="selectButton"></div>
    </div>    
    {/foreach}
</div>