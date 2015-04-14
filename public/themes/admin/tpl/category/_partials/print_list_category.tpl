<tr>
    <td>
        <a href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
            {$category->title|clearslash|escape:"html"}
        </a>
        <div class="listing-inline-actions">
            {acl isAllowed="CATEGORY_UPDATE"}
                {if $category->internal_category != 0 && $category->internal_category != 2}
                <a class="link" href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                </a>
                {/if}
            {/acl}
            {acl isAllowed="CATEGORY_DELETE"}
                {if $category->internal_category != 0}
                <a class="link empty-category"
                    href="{url name=admin_category_empty id=$category->pk_content_category}"
                    data-url="{url name=admin_category_empty id=$category->pk_content_category}"
                    data-title="{$category->title}"
                    title="Vaciar">
                    <i class="fa fa-fire"></i> {t}Empty{/t}
                </a>
                {/if}
                {if $category->internal_category != 0 && $category->internal_category != 2}
                <a class="link link-danger del-category"
                    href="{url name=admin_category_delete id=$category->pk_content_category}"
                    data-url="{url name=admin_category_delete id=$category->pk_content_category}"
                    data-title="{$category->title}"
                    title="Eliminar">
                    <i class="fa fa-trash-o"></i> {t}Remove{/t}
                </a>
                {/if}
            {/acl}
        </div>
    </td>
    <td>
        {$category->name}
    </td>
    <td class="center hidden-xs">
        {$num_contents['articles']|default:0}
    </td>
    <td class="center">
    {if $category->inmenu==1}
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <a class="btn btn-white" href="{url name=admin_category_toggleavailable id=$category->pk_content_category status=0}" title="En menu">
            <i class="fa fa-check text-success fa-lg"></i>
        </a>
        {/acl}
    {else}
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <a class="btn btn-white" href="{url name=admin_category_toggleavailable id=$category->pk_content_category status=1}" title="No en menu">
            <i class="fa fa-times text-danger fa-lg"></i>
        </a>
        {/acl}
    {/if}
    </td>
    {if $category->internal_category eq '1'}
    <td class="center hidden-xs">
        {if !is_array($category->params) || ($category->params['inrss'] eq 1 || !isset($category->params['inrss']))}
            <a class="btn btn-white" href="{url name=admin_category_togglerss id=$category->pk_content_category status=0}" title="En rss">
                <i class="fa fa-check text-success fa-lg"></i>
            </a>
        {else}
            <a class="btn btn-white" href="{url name=admin_category_togglerss id=$category->pk_content_category status=1}" title="No en rss">
                <i class="fa fa-times text-danger fa-lg"></i>
            </a>
        {/if}
    </td>
    {/if}
</tr>
{if count($subcategorys) >0}
    {assign var=i value=0}
    {foreach from=$subcategorys item=subcategory}
    <tr>
        <td style="padding-left: 20px;">
            <div class="row">
                <div class="col-md-2 right">
                    <span class="fa fa-angle-right"></span>
                </div>
                <div class="col-md-10">
                    <a href="{url name=admin_category_show id=$subcategory->pk_content_category}" title="Modificar">
                        {$subcategory->title}
                    </a>
                    <div class="listing-inline-actions">
                        {acl isAllowed="CATEGORY_UPDATE"}
                            <a class="link" href="{url name=admin_category_show id=$subcategory->pk_content_category}" title="Modificar">
                                <i class="fa fa-pencil"></i> {t}Edit{/t}
                            </a>
                        {/acl}
                        {acl isAllowed="CATEGORY_DELETE"}
                            {if $subcategory->internal_category==1 && $num_sub_contents[$i].articles!=0}
                            <a class="link empty-category"
                                href="{url name=admin_category_empty id=$subcategory->pk_content_category}"
                                data-url="{url name=admin_category_empty id=$subcategory->pk_content_category}"
                                data-title="{$subcategory->title}"
                                title="{t}Delete all the contents in this category{/t}">
                                <i class="icon-fire"></i> {t}Empty{/t}
                            </a>
                            {/if}
                            <a class="link link-danger del-category"
                                href="{url name=admin_category_delete id=$subcategory->pk_content_category}"
                                data-url="{url name=admin_category_delete id=$subcategory->pk_content_category}"
                                data-title="{$subcategory->title}"
                                title="{t}Delete category{/t}">
                                <i class="fa fa-trash-o"></i> {t}Remove{/t}
                            </a>
                        {/acl}
                    </div>
                </div>
            </div>
        </td>
        <td>
            {$subcategory->name}
        </td>
        <td class="center hidden-xs">
            {$num_sub_contents[$i].articles|default:0}
        </td>
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <td class="center">
        {if $subcategory->internal_category eq '1'}
            {if $subcategory->inmenu==1}
                <a class="btn btn-white" href="{url name=admin_category_toggleavailable id=$subcategory->pk_content_category status=0}" title="En menu">
                    <i class="fa fa-check text-success fa-lg"></i>
                </a>
            {else}
                <a class="btn btn-white" class="btn btn-white" href="{url name=admin_category_toggleavailable id=$subcategory->pk_content_category status=1}" title="No en menu">
                    <i class="fa fa-times text-danger fa-lg"></i>
                </a>
            {/if}
        {/if}
        </td>
        {/acl}
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <td class="center hidden-xs">
            {if $subcategory->params['inrss'] eq 1 || !isset($subcategory->params['inrss'])}
                <a class="btn btn-white" href="{url name=admin_category_togglerss id=$subcategory->pk_content_category status=0}" title="En rss">
                    <i class="fa fa-check text-success fa-lg"></i>
                </a>
            {else}
                <a class="btn btn-white" href="{url name=admin_category_togglerss id=$subcategory->pk_content_category status=1}" title="No en rss">
                    <i class="fa fa-times text-danger fa-lg"></i>
                </a>
            {/if}
        </td>
        {/acl}
    </tr>
    {capture}
    {$i++}
    {/capture}
  {/foreach}
{/if}
