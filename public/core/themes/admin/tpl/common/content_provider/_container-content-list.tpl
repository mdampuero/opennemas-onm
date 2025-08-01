<div class="contents">
    {if !empty($pagination)}
        <div class="pagination"> {$pagination} </div>
    {/if}

    {if !empty($contents)}
    <ul id='contentList'>
        {foreach from=$contents item=content}
            <li data-id="{$content->id}" data-type="{$contentType}" data-title="{$content->title|clearslash|html_attribute}">
                <input type="checkbox" class="hidden-element" name="selected">
                <span class="type">{t}{$content->content_type_l10n_name}{/t} -</span>
                <span class="date">{t}{$content->created|date_format:"%d-%m-%Y"}{/t} -</span>
                {$content->title|html_attribute}
                <span class="icon"><i class="fa fa-trash"></i></span>
            </li>
        {/foreach}
    </ul>
    {elseif $hidenoavailable != true}
        {t}No available contents{/t}
    {/if}
    {if !empty($pagination)}
        <div class="pagination"> {$pagination} </div>
    {/if}
</div>
