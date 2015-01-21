{if $categories}
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
        {/foreach}
    </tbody>
</table>
{else}
<div class="center">
    <h5>{t}No elements to sync in this server{/t}</h5>
    <p>{t}The given url has no elements to sync or it is not an Opennemas server.{/t}</p>
</div>
{/if}