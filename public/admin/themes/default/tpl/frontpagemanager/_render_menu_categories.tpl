{* print menu & more categories*}
<div class="clearfix">
    <div class="menu-categories">
        {include file="frontpagemanager/partials/_render_menu.tpl"}
    </div>

    <div  class="menu-other-categories">
        {assign var="url" value="/admin/controllers/frontpagemanager/frontpagemanager.php?action=list&amp;category="}
        <select name="category" id="categoryItem" onchange="submitFilters(this.form);">
            <option> --- </option>
            <option value="0" {if $category eq 0}selected{/if}> {t}Home{/t} </option>
            {section name=as loop=$allcategorys}
                {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                <option value="{$allcategorys[as]->pk_content_category}"  name="{$allcategorys[as]->title}"
                    {if $category eq $allcategorys[as]->pk_content_category} selected ="selected" {/if} >
                        {t 1=$allcategorys[as]->title}%1{/t}
                </option>
                {/acl}
                {section name=su loop=$subcat[as]}
                    {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                    <option value="{$subcat[as][su]->pk_content_category}" name="{$subcat[as][su]->title}"
                        {if $category eq $subcat[as][su]->pk_content_category} selected ="selected" {/if} >
                        &nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}
                    </option>
                    {/acl}
                {/section}
            {/section}
        </select>
    </div>
</div>

<script type="text/javascript">
function submitFilters(frm) {
    $('action').value='list';
    console.log($('categoryItem').value);
    $('category').value = $('categoryItem').value;
    frm.submit();
}
</script>
