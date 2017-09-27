<select name="{$name|default:'category'}" id="{$name|default:'category'}" required>
  <option value="" >{t}- Select a category -{/t}</option>
    {section name=as loop=$allcategorys}
        {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
            <option value="{$allcategorys[as]->pk_content_category}" data-name="{$allcategorys[as]->title}"
            {if $allcategorys[as]->inmenu eq 0} class="unavailable" disabled {/if}
            {if (($category == $allcategorys[as]->pk_content_category) && !is_object($item))
                || ($item->category eq $allcategorys[as]->pk_content_category)}selected{/if}>
                {$allcategorys[as]->title}
            </option>
        {/acl}
        {section name=su loop=$subcat[as]}
            {acl hasCategoryAccess=$subcat[as][su]->pk_content_category}
                {if $subcat[as][su]->internal_category eq 1}
                    <option value="{$subcat[as][su]->pk_content_category}" data-name="{$subcat[as][su]->title}"
                    {if $subcat[as][su]->inmenu eq 0} class="unavailable" disabled {/if}
                    {if ($category eq $subcat[as][su]->pk_content_category) && !is_object($item)
                    || $item->category eq $subcat[as][su]->pk_content_category}selected{/if} >
                        &nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}
                    </option>
                {/if}
            {/acl}
        {/section}
    {/section}
    <option value="20" data-name="{t}Unknown{/t}" {if ($category eq '20')}selected{/if}>{t}Unknown{/t}</option>
</select>
