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
              <a class="btn btn-primary" href="{url name=admin_category_create}" class="admin_add" accesskey="N" tabindex="1" id="create-button">
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
    <div class="grid simple ng-cloak">
      <div class="grid-body no-padding nav-tabs-tabdrop">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#article" aria-controls="article" role="tab" data-toggle="tab">{t}For articles{/t}</a></li>
          {is_module_activated name="ALBUM_MANAGER"}
            <li role="presentation"><a href="#album" aria-controls="album" role="tab" data-toggle="tab">{t}For albums{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="VIDEO_MANAGER"}
            <li role="presentation"><a href="#video" aria-controls="video" role="tab" data-toggle="tab">{t}For videos{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="KIOSKO_MANAGER"}
            <li role="presentation"><a href="#kiosko" aria-controls="kiosko" role="tab" data-toggle="tab">{t}For ePapers{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="POLL_MANAGER"}
            <li role="presentation"><a href="#poll" aria-controls="poll" role="tab" data-toggle="tab">{t}For polls{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="SPECIAL_MANAGER"}
            <li role="presentation"><a href="#special" aria-controls="special" role="tab" data-toggle="tab">{t}For Specials{/t}</a></li>
          {/is_module_activated}
          {is_module_activated name="BOOK_MANAGER"}
            <li role="presentation"><a href="#book" aria-controls="tab7" role="tab" data-toggle="tab">{t}For books{/t}</a></li>
          {/is_module_activated}
          {acl isAllowed="MASTER"}
            <li role="presentation"><a href="#master" aria-controls="master" role="tab" data-toggle="tab">{t}Internal{/t}</a></li>
          {/acl}
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="article">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th>{t}Title{/t}</th>
                  <th>{t}Internal name{/t}</th>
                  <th class="hidden-xs text-center" width="150">{t}Articles{/t}</th>
                  {acl isAllowed="CATEGORY_AVAILABLE"}
                  <th class="text-center" width="150">{t}Published{/t}</th>
                  <th class="hidden-xs text-center" width="150">{t}Show in rss{/t}</th>
                  {/acl}
                </tr>
              </thead>
              <tbody ng-init="languageData = {json_encode($language_data)|clear_json}; keys = ['title', 'name']">
                {foreach $categories as $category}
                  {if $category->internal_category eq '1' && empty($category->fk_content_category)}
                    {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                  {/if}
                {foreachelse}
                <tr>
                  <td class="empty">
                    {t}No available categories for listing{/t}
                  </td>
                </tr>
                {/foreach}
              </tbody>
            </table>
          </div>
          {is_module_activated name="ALBUM_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="album">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th class="hidden-xs text-center" width="100">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                      <th class="text-center" width="100">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {foreach $categories as $category}
                    {if $category->internal_category eq '7' && empty($category->fk_content_category)}
                      {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                    {/if}
                  {foreachelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          {/is_module_activated}
          {is_module_activated name="VIDEO_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="video">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th class="hidden-xs text-center" width="100">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th class="text-center" width="100">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {foreach $categories as $category}
                    {if $category->internal_category eq '9' && empty($category->fk_content_category)}
                      {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                    {/if}
                  {foreachelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          {/is_module_activated}
          {is_module_activated name="KIOSKO_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="kiosko">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th class="hidden-xs text-center" width="100">{t}Advertisements{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th class="text-center" width="100">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {foreach $categories as $category}
                    {if $category->internal_category eq '14' && empty($category->fk_content_category)}
                      {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                    {/if}
                  {foreachelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          {/is_module_activated}
          {is_module_activated name="POLL_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="poll">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th class="hidden-xs text-center" width="100">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th class="text-center" width="100">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {foreach $categories as $category}
                    {if $category->internal_category eq '11' && empty($category->fk_content_category)}
                      {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                    {/if}
                  {foreachelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          {/is_module_activated}
          {is_module_activated name="SPECIAL_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="special">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th class="hidden-xs text-center" width="100">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th class="text-center" width="100">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {foreach $categories as $category}
                    {if $category->internal_category eq '10' && empty($category->fk_content_category)}
                      {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                    {/if}
                  {foreachelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          {/is_module_activated}
          {is_module_activated name="BOOK_MANAGER"}
            <div role="tabpanel" class="tab-pane" id="book">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th class="hidden-xs text-center" width="100">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th class="text-center" width="100">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                  {foreach $categories as $category}
                    {if $category->internal_category eq '15' && empty($category->fk_content_category)}
                      {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                    {/if}
                  {foreachelse}
                  <tr>
                    <td class="empty">
                      {t}No available categories for listing{/t}
                    </td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
            </div>
          {/is_module_activated}
          {acl isAllowed="ONLY_MASTERS"}
            <div role="tabpanel" class="tab-pane" id="master">
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}Internal name{/t}</th>
                    <th class="hidden-xs text-center" width="100">{t}Articles{/t}</th>
                    {acl isAllowed="CATEGORY_AVAILABLE"}
                    <th class="text-center" width="100">{t}Published{/t}</th>
                    {/acl}
                  </tr>
                </thead>
                <tbody>
                {foreach $categories as $category}
                  {if $category->internal_category eq '0' && empty($category->fk_content_category)}
                    {include file="category/_partials/item.tpl" category=$category categories=$categories contents_count=$contents_count language_data=$language_data level=0}
                  {/if}
                {foreachelse}
                <tr>
                  <td class="empty">
                    {t}No available categories for listing{/t}
                  </td>
                </tr>
                {/foreach}
                </tbody>
              </table>
            </div>
          {/acl}
        </div>
      </div>
      <div class="grid-body"></div>
    </div>
  </div>
{/block}

{block name="modals"}
  {include file="category/modals/_modalDelete.tpl"}
  {include file="category/modals/_modalEmpty.tpl"}
{/block}
