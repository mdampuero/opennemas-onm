{if isset($contentTypeCategories) && !empty($contentTypeCategories)}
<select id="contentTypeCategories" data-href="{$contentProviderUrl}">
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

    <ul id='contentList'>
        {section name=n loop=$contents}
            <li data-id="{$contents[n]->id}" data-type="{$contentType}" data-title="{$contents[n]->title|clearslash}">
                <input type="checkbox" class="hidden-element" name="selected">
                <span class="type">{t}{$contents[n]->content_type_name|ucwords}{/t} -</span>
                <span class="date">{t}{$contents[n]->starttime|date_format:"%d-%m-%Y"}{/t} -</span>
                {$contents[n]->title}
                <span class="icon"><i class="icon-trash"></i></span>
            </li>
        {/section}
    </ul>
    {if !empty($pagination)}
        <div class="pagination"> {$pagination} </div>
    {/if}
</div>