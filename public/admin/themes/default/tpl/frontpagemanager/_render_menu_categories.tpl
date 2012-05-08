{* print menu & more categories*}
<div style="display:inline-block;width:75%;">
    {include file="frontpagemanager/partials/_render_menu.tpl"}
</div>

<div style="display:inline-block;width:25%; float:right;">
    {assign var="url" value="/admin/controllers/frontpagemanager/frontpagemanager.php?action=list&amp;category="}
    <select name="category" id="categoryItem" onchange="submitFilters(this.form);">
        <option> --- </option>
        <option value="0" {if $category eq 0}selected{/if}> {t}Home{/t} </option>
        {section name=as loop=$allcategorys}
            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >
                    {t 1=$allcategorys[as]->title}%1{/t}
            </option>
            {/acl}
            {section name=su loop=$subcat[as]}
                {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">
                    &nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}
                </option>
                {/acl}
            {/section}
        {/section}
    </select>
</div>


    <script type="text/javascript">
        function submitFilters(frm) {
            $('action').value='list';
            console.log($('categoryItem').value);
            $('category').value = $('categoryItem').value;
            frm.submit();
        }
    </script>
