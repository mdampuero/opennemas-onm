<table class="table table-hover table-condensed">
    <tr>
        <td>
            <strong>{t}Categories to availables to sync{/t}</strong>
        </td>
    </tr>
    <tbody>
        {section name=d loop=$categories}
        <tr>
            <td>
                <input type="checkbox"
                    name="categories[]"
                    value="{$categories[d]->link}"
                    {foreach from=$categories_checked item=cat}
                            {if $cat eq $categories[d]->link}
                                checked="checked"
                            {/if}
                    {/foreach}
                    />
                {$categories[d]->title|ucfirst}
            </td>
        </tr>
        {sectionelse}
        <tr>
            <td class="empty">
                <h2>
                    <b>{t}There is no elements to sync{/t}</b>
                </h2>
                <p>{t}Check if the given site url is correct.{/t}</p>
            </td>
        </tr>
        {/section}
    </tbody>
</table>
