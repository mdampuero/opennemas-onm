{extends file="base/admin.tpl"}

{block name="content"}
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-bookmark"></i>
              {t}Categories{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_categories_config}" class="admin_add" title="{t}Config categories module{/t}">
                <span class="fa fa-cog"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            {acl isAllowed="CATEGORY_CREATE"}
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=admin_category_create}" class="admin_add" accesskey="N" tabindex="1">
                <span class="fa fa-plus"></span>
                {t}Create{/t}
              </a>
            </li>
            {/acl}
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    {render_messages}
    <div class="grid simple">
      <div class="grid-body no-padding">
        <tabset>
          <tab heading="{t}For articles{/t}" class="no-padding active">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th>{t}Title{/t}</th>
                  <th>{t}Internal name{/t}</th>
                  <th style="width:15px;">{t}Articles{/t}</th>
                  {acl isAllowed="CATEGORY_AVAILABLE"}
                  <th style="width:15px;">{t}Available{/t}</th>
                  <th style="width:15px;" class="nowrap">{t}Show in rss{/t}</th>
                  {/acl}
                </tr>
              </thead>
              <tbody>
                {section name=c loop=$categorys}
                {if $categorys[c]->internal_category eq '1'}
                {include file="category/_partials/print_list_category.tpl"
                category=$categorys[c]
                subcategorys=$subcategorys[c]
                num_contents=$num_contents[c]
                num_sub_contents=$num_sub_contents[c]|default:array()}
                {/if}
                {sectionelse}
                <tr>
                  <td class="empty">
                    {t}No available categories for listing{/t}
                  </td>
                </tr>
                {/section}
              </tbody>
            </table>
          </tab>
          {is_module_activated name="ALBUM_MANAGER"}
            <tab heading="{t}For albums{/t}" class="no-padding">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Available{/t}</th>
                    {/acl}
                    <th style="width:100px;"></th>
                  </tr>
                </thead>
                <tbody>
                  {section name=c loop=$categorys}
                  {if $categorys[c]->internal_category eq '7'}
                  {include file="category/_partials/print_list_category.tpl"
                  category=$categorys[c]
                  subcategorys=$subcategorys[c]
                  num_contents=$num_contents[c]
                  num_sub_contents=$num_sub_contents[c]}
                  {/if}
                  {sectionelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/section}
                </tbody>
              </table>
            </tab>
          {/is_module_activated}
          {is_module_activated name="VIDEO_MANAGER"}
            <tab heading="{t}For videos{/t}" class="no-padding">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Available{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {section name=c loop=$categorys}
                  {if $categorys[c]->internal_category eq '9'}
                  {include file="category/_partials/print_list_category.tpl"
                  category=$categorys[c]
                  subcategorys=$subcategorys[c]
                  num_contents=$num_contents[c]
                  num_sub_contents=$num_sub_contents[c]}
                  {/if}
                  {sectionelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/section}

                </tbody>
              </table>
            </tab>
          {/is_module_activated}
          {is_module_activated name="KIOSKO_MANAGER"}
            <tab heading="{t}For ePapers{/t}" class="no-padding">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;">{t}Advertisements{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Available{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {section name=c loop=$categorys}
                  {if $categorys[c]->internal_category eq '14'}
                  {include file="category/_partials/print_list_category.tpl"
                  category=$categorys[c]
                  subcategorys=$subcategorys[c]
                  num_contents=$num_contents[c]
                  num_sub_contents=$num_sub_contents[c]}
                  {/if}
                  {sectionelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/section}
                </tbody>
              </table>
            </tab>
          {/is_module_activated}
          {is_module_activated name="POLL_MANAGER"}
            <tab heading="{t}For polls{/t}" class="no-padding">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Available{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {section name=c loop=$categorys}
                  {if $categorys[c]->internal_category eq '11'}
                  {include file="category/_partials/print_list_category.tpl"
                  category=$categorys[c]
                  subcategorys=$subcategorys[c]
                  num_contents=$num_contents[c]
                  num_sub_contents=$num_sub_contents[c]}
                  {/if}
                  {sectionelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/section}
                </tbody>
              </table>
            </tab>
          {/is_module_activated}
          {is_module_activated name="SPECIAL_MANAGER"}
            <tab heading="{t}For Specials{/t}" class="no-padding">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Available{/t}</th>
                    {/acl}
                    <th style="width:70px;"></th>
                  </tr>
                </thead>
                <tbody>
                  {section name=c loop=$categorys}
                  {if $categorys[c]->internal_category eq '10'}
                  {include file="category/_partials/print_list_category.tpl"
                  category=$categorys[c]
                  subcategorys=$subcategorys[c]
                  num_contents=$num_contents[c]
                  num_sub_contents=$num_sub_contents[c]}
                  {/if}
                  {sectionelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/section}
                </tbody>
              </table>
            </tab>
          {/is_module_activated}
          {is_module_activated name="BOOK_MANAGER"}
            <tab heading="{t}For books{/t}" class="no-padding">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Available{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {section name=c loop=$categorys}
                  {if $categorys[c]->internal_category eq '15'}
                  {include file="category/_partials/print_list_category.tpl"
                  category=$categorys[c]
                  subcategorys=$subcategorys[c]
                  num_contents=$num_contents[c]
                  num_sub_contents=$num_sub_contents[c]}
                  {/if}
                  {sectionelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/section}
                </tbody>
              </table>
            </tab>
          {/is_module_activated}
        </tabset>
      </div>
    </div>
  </div>
  {include file="category/modals/_modalDelete.tpl"}
  {include file="category/modals/_modalEmpty.tpl"}
{/block}
