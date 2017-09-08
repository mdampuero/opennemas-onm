{if $category->fk_content_category == 0}
  <script>
      var languageData = {$language_data|@json_encode};
  </script>
  <tr>
      <td>
          <a href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
              {multi_option_adapter field=$category->title params=$language_data}
          </a>
          <div class="listing-inline-actions">
              {is_module_activated name="es.openhost.module.multilanguage"}
                <translator language-data="[% languageData %]" link="{url name=admin_category_show id=$category->pk_content_category}" />
              {/is_module_activated}
              {acl isAllowed="CATEGORY_UPDATE"}
                  {if $category->internal_category != 0 && $category->internal_category != 2}
                  <a class="link" href="{url name=admin_category_show id=$category->pk_content_category}" title="Modificar">
                      <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  {/if}
              {/acl}
              {acl isAllowed="CATEGORY_DELETE"}
                   {if $category->internal_category != 0 && $category->internal_category != 2}
                  <a class="link link-danger del-category"
                      href="{url name=admin_category_delete id=$category->pk_content_category}"
                      data-url="{url name=admin_category_delete id=$category->pk_content_category}"
                      data-title="{$category->title[$language_data['default']]}"
                      title="Eliminar">
                      <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                  </a>
                  {/if}
                  {if $category->internal_category != 0 && array_key_exists($category->id, $contents_count['articles']) &&  $contents_count['articles'][$category->id] !== 0}
                  <a class="link empty-category"
                      href="{url name=admin_category_empty id=$category->pk_content_category}"
                      data-url="{url name=admin_category_empty id=$category->pk_content_category}"
                      data-title="{$category->title[$language_data['default']]}"
                      title="Vaciar">
                      <i class="fa fa-fire m-r-5"></i>{t}Empty{/t}
                  </a>
                  {/if}
              {/acl}
          </div>
      </td>
      <td>
          {$category->name[$language_data['default']]}
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

  {foreach $categories as $subcategory}
  {if $subcategory->fk_content_category == $category->id}
  <tr>
      <td style="padding-left: 10px;">
          <div class="row">
              <div class="col-md-1 right">
                  <span class="fa fa-angle-right"></span>
              </div>
              <div class="col-md-11">
                  <a href="{url name=admin_category_show id=$subcategory->pk_content_category}" title="Modificar">
                      {$subcategory->title}
                  </a>
                  <div class="listing-inline-actions">
                      {acl isAllowed="CATEGORY_UPDATE"}
                          <a class="link" href="{url name=admin_category_show id=$subcategory->pk_content_category}" title="Modificar">
                              <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                          </a>
                      {/acl}
                      {acl isAllowed="CATEGORY_DELETE"}
                         {if $subcategory->internal_category != 0 && array_key_exists($subcategory->id, $contents_count['articles']) &&  $contents_count['articles'][$subcategory->id] !== 0}
                          <a class="link empty-category"
                              href="{url name=admin_category_empty id=$subcategory->pk_content_category}"
                              data-url="{url name=admin_category_empty id=$subcategory->pk_content_category}"
                              data-title="{$subcategory->title[$language_data['default']]}"
                              title="{t}Delete all the contents in this category{/t}">
                            <i class="fa fa-fire m-r-5"></i>{t}Empty{/t}
                          </a>
                          {/if}
                          <a class="link link-danger del-category"
                              href="{url name=admin_category_delete id=$subcategory->pk_content_category}"
                              data-url="{url name=admin_category_delete id=$subcategory->pk_content_category}"
                              data-title="{$subcategory->title[$language_data['default']]}"
                              title="{t}Delete category{/t}">
                              <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                          </a>
                      {/acl}
                  </div>
              </div>
          </div>
      </td>
      <td>
          {$subcategory->name[$language_data['default']]}
      </td>
      <td class="center hidden-xs">
          {$contents_count['articles'][$subcategory->id]|default:0}
      </td>
      {acl isAllowed="CATEGORY_AVAILABLE"}
      <td class="center">
      {if $subcategory->internal_category eq '1'}
          {if $subcategory->inmenu==1}
              <a class="btn btn-white" href="{url name=admin_category_toggleavailable id=$subcategory->pk_content_category status=0}" title="En menu">
                  <i class="fa fa-check text-success"></i>
              </a>
          {else}
              <a class="btn btn-white" class="btn btn-white" href="{url name=admin_category_toggleavailable id=$subcategory->pk_content_category status=1}" title="No en menu">
                  <i class="fa fa-times text-danger"></i>
              </a>
          {/if}
      {/if}
      </td>
      {/acl}
      {acl isAllowed="CATEGORY_AVAILABLE"}
      <td class="center hidden-xs">
          {if $subcategory->params['inrss'] eq 1 || !isset($subcategory->params['inrss'])}
              <a class="btn btn-white" href="{url name=admin_category_togglerss id=$subcategory->pk_content_category status=0}" title="En rss">
                  <i class="fa fa-check text-success"></i>
              </a>
          {else}
              <a class="btn btn-white" href="{url name=admin_category_togglerss id=$subcategory->pk_content_category status=1}" title="No en rss">
                  <i class="fa fa-times text-danger"></i>
              </a>
          {/if}
      </td>
      {/acl}
  </tr>
  {/if}
  {/foreach}
{/if}
