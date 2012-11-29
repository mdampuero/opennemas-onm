<table class="table table-hover table-condensed">
    <tbody>
        {foreach name=d from=$categories item=category}
        <tr>
            <td>
                <label>
                    <input type="checkbox" name="categories[]" value="{$category->link}"
                        {if in_array($category->link, $categories_checked)}checked="checked"{/if} />
                    {$category->title|ucfirst}
                </label>
            </td>
        </tr>
        {if !empty($category->submenu)}
            {foreach name=d from=$category->submenu item=subcategory}
            <tr>
                <td>
                    <label>
                        <blockquote>
                            <input type="checkbox" name="categories[]" value="{$subcategory->link}"
                            {if in_array($subcategory->link, $categories_checked)}checked="checked"{/if} />
                            {$subcategory->title|ucfirst}
                        </blockquote>
                    </label>
                </td>
            </tr>
            {/foreach}
        {/if}
        {foreachelse}
        <tr>
            <td class="empty">
                <h4>{t}There is no elements to sync{/t}</h4>
                <p>{t}Check if the given site url is correct.{/t}</p>
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
