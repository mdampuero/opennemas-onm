{if isset($contentTypeCategories) && !empty($contentTypeCategories)}
<select id="contentTypeCategories" data-href="{$smarty.server.SCRIPT_NAME}?action={$action|default:'content-list-provider'}">

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
    <ul id='contentList'>
        {section name=n loop=$contents}
            <li data-id="{$contents[n]->id}" data-type="{$contentType}" data-title="{$contents[n]->title|clearslash}">
                <input type="checkbox" class="hidden-element" name="selected">
                <span class="type">{t}{$contentType}{/t} -</span>
                <span class="date">{t}{$contents[n]->starttime|date_format:"%d-%m-%Y"}{/t} -</span>
                {$contents[n]->title}
            </li>
        {/section}
    </ul>
{if !empty($pagination)}
    <div class="pagination"> {$pagination} </div>
{/if}