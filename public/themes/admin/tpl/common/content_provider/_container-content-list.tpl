{if isset($contentTypeCategories) && !empty($contentTypeCategories)}
<select id="contentTypeCategories" class="selector" data-href="{$contentProviderUrl}">
        <option value="0">{t}-- All categories --{/t}</option>
        {section name=as loop=$contentTypeCategories}
            <option value="{$contentTypeCategories[as]->pk_content_category}"
                {if $category eq $contentTypeCategories[as]->pk_content_category}selected{/if}>
                {$contentTypeCategories[as]->title}
            </option>
            {section name=su loop=$subcat[as]}
                    <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
            {/section}
        {/section}
</select>
{/if}

<div class="contents">
    {if !empty($pagination)}
        <div class="pagination"> {$pagination} </div>
    {/if}

    {if count($contents) > 0}
    <ul id='contentList'>
        {foreach from=$contents item=content}
            <li data-id="{$content->id}" data-type="{$contentType}" data-title="{$content->title|clearslash|clean_for_html_attributes}">
                <input type="checkbox" class="hidden-element" name="selected">
                <span class="type">{t}{$content->content_type_l10n_name}{/t} -</span>
                <span class="date">{t}{$content->created|date_format:"%d-%m-%Y"}{/t} -</span>
                {$content->title|clean_for_html_attributes}
                <span class="icon"><i class="icon-trash"></i></span>
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
