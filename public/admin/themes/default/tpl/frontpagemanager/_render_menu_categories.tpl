<div class="clearfix">
    <div class="menu-categories pull-left">
        {include file="frontpagemanager/partials/_render_menu.tpl"}
    </div>
    <div class="pull-right">
        <div class="menu-other-categories btn-group">
            <select name="category" id="categoryItem">
                <option value="0" {if $category eq 0}selected{/if}>{t}Home{/t}</option>
                {section name=as loop=$allcategorys}
                    {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                    <option value="{$allcategorys[as]->pk_content_category}"
                        {if $category eq $allcategorys[as]->pk_content_category} selected ="selected" {/if} >
                            {t 1=$allcategorys[as]->title}%1{/t}
                    </option>
                    {/acl}
                    {section name=su loop=$subcat[as]}
                        {acl hasCategoryAccess=$subcat[as]->pk_content_category}
                        <option value="{$subcat[as][su]->pk_content_category}"
                            {if $category eq $subcat[as][su]->pk_content_category} selected ="selected" {/if} >
                            &nbsp;&nbsp;|_&nbsp;&nbsp;{t 1=$subcat[as][su]->title}%1{/t}
                        </option>
                        {/acl}
                    {/section}
                {/section}
            </select>
        </div>
        <div class="btn-group" id="frontpage-settings">
          <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="icon-cog"></i>
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu pull-right">
            <li><a tabindex="-1" href="#" id="pick-layout">{t}Pick layout{/t}</a></li>
          </ul>
        </div>
    </div>
</div>

<script type="text/javascript">
var frontpage_url= '{url name=admin_frontpage_list}'
jQuery(document).ready(function($) {
    $('#categoryItem').on('change', function(){
        var category_value = $('#categoryItem option:checked').val();
        window.location = frontpage_url+'/'+category_value;
    });
})
</script>
