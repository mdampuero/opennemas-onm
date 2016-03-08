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
                <span class="fa fa-cog fa-lg"></span>
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
    <div class="grid simple">
      <div class="grid-body no-padding">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">{t}For articles{/t}</a></li>
          {is_module_activated name="ALBUM_MANAGER"}
            <li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">{t}For albums{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="VIDEO_MANAGER"}
            <li role="presentation"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">{t}For videos{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="KIOSKO_MANAGER"}
            <li role="presentation"><a href="#tab4" aria-controls="tab4" role="tab" data-toggle="tab">{t}For ePapers{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="POLL_MANAGER"}
            <li role="presentation"><a href="#tab5" aria-controls="tab5" role="tab" data-toggle="tab">{t}For polls{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="SPECIAL_MANAGER"}
            <li role="presentation"><a href="#tab6" aria-controls="tab6" role="tab" data-toggle="tab">{t}For Specials{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="BOOK_MANAGER"}
            <li role="presentation"><a href="#tab7" aria-controls="tab7" role="tab" data-toggle="tab">{t}For books{/t}</a></li>
          {/is_module_activated}
          {acl isAllowed="ONLY_MASTERS"}
            <li role="presentation"><a href="#tab8" aria-controls="tab8" role="tab" data-toggle="tab">{t}Internal{/t}</a></li>
          {/acl}
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tab1">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th>{t}Title{/t}</th>
                  <th>{t}Internal name{/t}</th>
                  <th style="width:15px;" class="hidden-xs">{t}Articles{/t}</th>
                  {acl isAllowed="CATEGORY_AVAILABLE"}
                  <th style="width:15px;">{t}Published{/t}</th>
                  <th style="width:15px;" class="nowrap hidden-xs">{t}Show in rss{/t}</th>
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
          </div>
          {is_module_activated name="ALBUM_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="tab2">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;" class="hidden-xs">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Published{/t}</th>
                    {/acl}
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
            </div>
          {/is_module_activated}
          {is_module_activated name="VIDEO_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="tab3">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;" class="hidden-xs">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Published{/t}</th>
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
            </div>
          {/is_module_activated}
          {is_module_activated name="KIOSKO_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="tab4">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;" class="hidden-xs">{t}Advertisements{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Published{/t}</th>
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
            </div>
          {/is_module_activated}
          {is_module_activated name="POLL_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="tab5">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;" class="hidden-xs">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Published{/t}</th>
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
            </div>
          {/is_module_activated}
          {is_module_activated name="SPECIAL_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="tab6">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;" class="hidden-xs">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Published{/t}</th>
                    {/acl}
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
            </div>
          {/is_module_activated}
          {is_module_activated name="BOOK_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="tab7">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;" class="hidden-xs">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Published{/t}</th>
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
            </div>
          {/is_module_activated}
          {acl isAllowed="ONLY_MASTERS"}
            <div role="tabpanel" class="tab-pane" id="tab8">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th style="width:15px;" class="hidden-xs">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th style="width:15px;">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {section name=c loop=$categorys}
                  {if $categorys[c]->internal_category eq '0'}
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
            </div>
          {/acl}
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  {include file="category/modals/_modalDelete.tpl"}
  {include file="category/modals/_modalEmpty.tpl"}
{/block}
