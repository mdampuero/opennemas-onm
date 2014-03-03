<tr>
    <td>
        <a href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
            {$category->title|clearslash|escape:"html"}
        </a>
    </td>
    <td>
        {$category->name}
    </td>
    <td class="center">
        {$num_contents['articles']|default:0}
    </td>
    <td class="center">
        {$num_contents['photos']|default:0}
    </td>
    <td class="center">
        {$num_contents['advertisements']|default:0}
    </td>
    <td class="center">
    {if $category->inmenu==1}
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <a href="{url name=admin_category_toggleavailable id=$category->pk_content_category status=0}" title="En menu">
            <img src="{$params.IMAGE_DIR}publish_g.png" alt="Publicado" />
        </a>
        {/acl}
    {else}
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <a href="{url name=admin_category_toggleavailable id=$category->pk_content_category status=1}" title="No en menu">
            <img src="{$params.IMAGE_DIR}publish_r.png" alt="Pendiente" />
        </a>
        {/acl}
    {/if}
    </td>
    {if $category->internal_category eq '1'}
    <td class="center">
        {if $category->params['inrss'] eq 1 || !isset($category->params['inrss'])}
            <a href="{url name=admin_category_togglerss id=$category->pk_content_category status=0}" title="En rss">
                <img src="{$params.IMAGE_DIR}publish_g.png" alt="Publicado" />
            </a>
        {else}
            <a href="{url name=admin_category_togglerss id=$category->pk_content_category status=1}" title="No en rss">
                <img src="{$params.IMAGE_DIR}publish_r.png" alt="Pendiente" />
            </a>
        {/if}
    </td>
    {/if}
    <td class="right nowrap">
        <div class="btn-group">
            {acl isAllowed="CATEGORY_UPDATE"}
                {if $category->internal_category != 0 && $category->internal_category != 2}
                <a class="btn btn-mini" href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
                    <i class="icon-pencil"></i>
                </a>
                {/if}
            {/acl}
            {acl isAllowed="CATEGORY_DELETE"}
                {if $category->internal_category != 0}
                <a class="btn btn-mini empty-category"
                    href="{url name=admin_category_empty id=$category->pk_content_category}"
                    data-url="{url name=admin_category_empty id=$category->pk_content_category}"
                    data-title="{$category->title}"
                    title="Vaciar">
                    <i class="icon-fire"></i>
                </a>
                {/if}
                {if $category->internal_category != 0 && $category->internal_category != 2}
                <a class="btn btn-mini btn-danger del-category"
                    href="{url name=admin_category_delete id=$category->pk_content_category}"
                    data-url="{url name=admin_category_delete id=$category->pk_content_category}"
                    data-title="{$category->title}"
                    title="Eliminar">
                    <i class="icon-trash icon-white"></i>
                </a>
                {/if}
            {/acl}
        </div>
    </td>
</tr>
{if count($subcategorys) >0}
    {assign var=i value=0}
    {foreach from=$subcategorys item=subcategory}
    <tr>
        <td style="padding-left: 20px;">
            &rArr;
            <a href="{url name=admin_category_show id=$subcategory->pk_content_category}" title="Modificar">
                <strong>{$subcategory->title}</strong>
            </a>
        </td>
        <td>
            {$subcategory->name}
        </td>
        <td class="center">
            {$num_sub_contents[$i].articles|default:0}
        </td>
        <td class="center">
            {$num_sub_contents[$i].photos|default:0}
        </td>
        <td class="center">
            {$num_sub_contents[$i].advertisements|default:0}
        </td>
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <td class="center">
        {if $subcategory->internal_category eq '1'}
            {if $subcategory->inmenu==1}
                <a href="{url name=admin_category_toggleavailable id=$subcategory->pk_content_category status=0}" title="En menu">
                    <img src="{$params.IMAGE_DIR}publish_g.png" alt="Publicado" />
                </a>
            {else}
                <a href="{url name=admin_category_toggleavailable id=$subcategory->pk_content_category status=1}" title="No en menu">
                    <img src="{$params.IMAGE_DIR}publish_r.png" alt="Pendiente" />
                </a>
            {/if}
        {/if}
        </td>
        {/acl}
        {acl isAllowed="CATEGORY_AVAILABLE"}
        <td class="center">
            {if $subcategory->params['inrss'] eq 1 || !isset($subcategory->params['inrss'])}
                <a href="{url name=admin_category_togglerss id=$subcategory->pk_content_category status=0}" title="En rss">
                    <img src="{$params.IMAGE_DIR}publish_g.png" alt="Publicado" />
                </a>
            {else}
                <a href="{url name=admin_category_togglerss id=$subcategory->pk_content_category status=1}" title="No en rss">
                    <img src="{$params.IMAGE_DIR}publish_r.png" alt="Pendiente" />
                </a>
            {/if}
        </td>
        {/acl}
        <td class="right nowrap">
            <div class="btn-group">
                {acl isAllowed="CATEGORY_UPDATE"}
                    <a class="btn btn-mini" href="{url name=admin_category_show id=$subcategory->pk_content_category}" title="Modificar">
                        <i class="icon-pencil"></i>
                    </a>
                {/acl}
                {acl isAllowed="CATEGORY_DELETE"}
                    {if ($subcategory->internal_category==1) && ($num_sub_contents[$i].articles!=0 || $num_sub_contents[$i].photos!=0 || $num_sub_contents[$i].advertisements!=0)}
                    <a class="btn btn-mini empty-category"
                        href="{url name=admin_category_empty id=$subcategory->pk_content_category}"
                        data-url="{url name=admin_category_empty id=$subcategory->pk_content_category}"
                        data-title="{$subcategory->title}"
                        title="{t}Delete all the contents in this category{/t}">
                        <i class="icon-fire"></i>
                    </a>
                    {/if}
                    <a class="btn btn-mini btn-danger del-category"
                        href="{url name=admin_category_delete id=$subcategory->pk_content_category}"
                        data-url="{url name=admin_category_delete id=$subcategory->pk_content_category}"
                        data-title="{$subcategory->title}"
                        title="{t}Delete category{/t}">
                        <i class="icon-trash icon-white"></i>
                    </a>
                {/acl}
            </div>
        </td>
    </tr>
    {capture}
    {$i++}
    {/capture}
  {/foreach}
{/if}
