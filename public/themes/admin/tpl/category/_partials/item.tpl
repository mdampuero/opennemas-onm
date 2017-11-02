<tr>
  <td>
    <div class="row" style="padding-left: {$level * 15}px;">
      <div class="col-md-12">
        {if $level > 0}<span class="fa fa-angle-right"></span>{/if}
        <a href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
          {localize_filter field=$category->title params=$language_data}
        </a>
        <div class="listing-inline-actions" ng-init="mainTranslationField = {json_encode($category)|clear_json}">
          {acl isAllowed="CATEGORY_UPDATE"}
            {if $category->internal_category != 0 && $category->internal_category != 2}
              {if $multilanguage_enable}
                <translator ng-model="mainTranslationField" link="{url name=admin_category_show id=$category->pk_content_category}" options="languageData" item="mainTranslationField" text="{t}Edit{/t}" keys="keys"></translator>
              {else}
                <a class="link" href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
                  <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                </a>
              {/if}
            {/if}
          {/acl}
          {acl isAllowed="CATEGORY_DELETE"}
            {if $category->internal_category != 0 && $category->internal_category != 2}
              <a class="link link-danger del-category"
                 href="{url name=admin_category_delete id=$category->pk_content_category}"
                 data-url="{url name=admin_category_delete id=$category->pk_content_category}"
                 data-title="{localize_filter field=$category->title params=$language_data}"
                 title="Eliminar">
                 <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
              </a>
            {/if}
            {if $category->internal_category != 0 && array_key_exists($category->id, $contents_count['articles']) &&  $contents_count['articles'][$category->id] !== 0}
              <a class="link empty-category"
                 href="{url name=admin_category_empty id=$category->pk_content_category}"
                 data-url="{url name=admin_category_empty id=$category->pk_content_category}"
                 data-title="{localize_filter field=$category->title params=$language_data}"
                 title="Vaciar">
                 <i class="fa fa-fire m-r-5"></i>{t}Empty{/t}
              </a>
            {/if}
          {/acl}
        </div>
      </div>
    </div>
  </td>
  <td>
    {$category->name}
  </td>
  <td class="hidden-xs text-center">
    {$contents_count['articles'][$category->id]|default:0}
  </td>
  <td class="text-center">
    {if $category->inmenu==1}
      {acl isAllowed="CATEGORY_AVAILABLE"}
        <a class="btn btn-white" href="{url name=admin_category_toggleavailable id=$category->pk_content_category status=0}" title="En menu">
          <i class="fa fa-check text-success"></i>
        </a>
      {/acl}
    {else}
      {acl isAllowed="CATEGORY_AVAILABLE"}
        <a class="btn btn-white" href="{url name=admin_category_toggleavailable id=$category->pk_content_category status=1}" title="No en menu">
          <i class="fa fa-times text-danger"></i>
        </a>
      {/acl}
    {/if}
  </td>
  {if $category->internal_category eq '1'}
    <td class="hidden-xs text-center">
      {if !is_array($category->params) || ($category->params['inrss'] eq 1 || !isset($category->params['inrss']))}
        <a class="btn btn-white" href="{url name=admin_category_togglerss id=$category->pk_content_category status=0}" title="En rss">
          <i class="fa fa-check text-success"></i>
        </a>
      {else}
        <a class="btn btn-white" href="{url name=admin_category_togglerss id=$category->pk_content_category status=1}" title="No en rss">
          <i class="fa fa-times text-danger"></i>
        </a>
      {/if}
    </td>
  {/if}
</tr>
{foreach $category->subcategories as $id}
  {include file="category/_partials/item.tpl" category=$categories[$id] categories=$categories contents_count=$contents_count level=($level + 1)}
 {/foreach}
