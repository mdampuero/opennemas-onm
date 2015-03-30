{extends file="base/admin.tpl"}

{block name="content"}
<div method="post" ng-app="BackendApp" ng-controller="TrashListCtrl" ng-init="init('content', { in_litter: 1, title_like: '', content_type_name: -1 }, 'created', 'desc', 'backend_ws_contents_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">

  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-trash-o"></i>
              {t}Trash{/t}
            </h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              {acl isAllowed="ARTICLE_CREATE"}
              <button class="btn btn-danger" type="button" ng-click="removeAll()">
                <i class="fa fa-trash-o"></i>
                {t}Remove all{/t}
              </button>
              {/acl}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section pull-left">
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="deselectAll()" tooltip="Clear selection" tooltip-placement="right"type="button">
              <i class="fa fa-arrow-left fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <h4>
              [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
            </h4>
          </li>
        </ul>
        <ul class="nav quick-section pull-right">
          {acl isAllowed="TRASH_ADMIN"}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="restoreFromTrashSelected()" tooltip="{t}Restore{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-retweet fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="removePermanentlySelected()" tooltip="{t}Remove{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-trash-o fa-lg"></i>
            </button>
          </li>
          {/acl}
        </ul>
      </div>
    </div>
  </div>

  <div class="page-navbar filters-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="m-r-10 input-prepend inside search-input no-boarder">
            <span class="add-on">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="no-boarder" type="text" name="title" ng-model="criteria.title_like" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by name{/t}" />
          </li>
          <li class="quicklinks hidden-xs">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks hidden-xs">
            <select id="content_type_name" ng-model="criteria.content_type_name" data-label="{t}Content Type{/t}" class="select2">
              <option value="-1">{t}-- All --{/t}</option>

              {is_module_activated name="ARTICLE_MANAGER"}
              {acl isAllowed="ARTICLE_TRASH"}
              <option value="article">{t}Articles{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="OPINION_MANAGER"}
              {acl isAllowed="OPINION_TRASH"}
              <option value="opinion">{t}Opinions{/t}</option>
              {/acl}{/is_module_activated}

              {is_module_activated name="OPINION_MANAGER"}
              {acl isAllowed="LETTER_TRASH"}
              <option value="letter">{t}Letters{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="ADS_MANAGER"}
              {acl isAllowed="ADVERTISEMENT_TRASH"}
              <option value="advertisement">{t}Advertisements{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="KIOSKO_MANAGER"}
              {acl isAllowed="KIOSKO_TRASH"}
              <option value="kiosko">{t}Covers{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="ALBUM_MANAGER"}
              {acl isAllowed="ALBUM_TRASH"}
              <option value="album">{t}Albums{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="IMAGE_MANAGER"}
              {acl isAllowed="PHOTO_TRASH"}
              <option value="photo">{t}Images{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="VIDEO_MANAGER"}
              {acl isAllowed="VIDEO_TRASH"}
              <option value="video">{t}Videos{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="FILE_MANAGER"}
              {acl isAllowed="FILE_DELETE"}
              <option value="attachment">{t}Files{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="POLL_MANAGER"}
              {acl isAllowed="POLL_DELETE"}
              <option value="poll">{t}Polls{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="SPECIAL_MANAGER"}
              {acl isAllowed="SPECIAL_DELETE"}
              <option value="special">{t}Specials{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="STATIC_PAGES_MANAGER"}
              {acl isAllowed="STATIC_PAGE_DELETE"}
              <option value="static_page">{t}Static Pages{/t}</option>
              {/acl}
              {/is_module_activated}

              {is_module_activated name="WIDGET_MANAGER"}
              {acl isAllowed="WIDGET_DELETE"}
              <option value="widget">{t}Widgets{/t}</option>
              {/acl}
              {/is_module_activated}
            </select>
          </li>
          <li class="quicklinks hidden-xs">
            <select class="select2 input-medium" name="status" ng-model="pagination.epp" data-label="{t}View{/t}">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </li>
        </ul>
        <ul class="nav quick-section pull-right simple-pagination ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <span class="info">
              [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
            </span>
          </li>
          <li class="quicklinks form-inline pagination-links">
            <div class="btn-group">
              <button class="btn btn-white" ng-click="goToPrevPage()" ng-disabled="isFirstPage()" type="button">
                <i class="fa fa-chevron-left"></i>
              </button>
              <button class="btn btn-white" ng-click="goToNextPage()" ng-disabled="isLastPage()" type="button">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="content">
    {render_messages}

    <div class="grid simple">
      <div class="grid-body no-padding">
        <div class="spinner-wrapper" ng-if="loading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
          <div class="center">
            <h4>{t}No contents in the trash that matches your search.{/t}</h4>
          </div>
        </div>
        <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
          <table class="table table-hover no-margin">
            <thead>
               <tr>
                <th class="checkbox-cell">
                  <div class="checkbox checkbox-default">
                    <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                    <label for="select-all"></label>
                  </div>
                </th>
                <th class='left'>{t}Title{/t}</th>
                <th class="left hidden-xs" style="width:110px;">{t}Date{/t}</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
                <td class="checkbox-cell">
                  <div class="checkbox check-default">
                    <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                    <label for="checkbox[%$index%]"></label>
                  </div>
                </td>
                <td>
                  <strong>[% content.content_type_l10n_name %]</strong> - [% content.title %]
                  <div class="visible-xs small-text">
                    <strong>{t}Date{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                  </div>
                  <div class="listing-inline-actions">
                    <a ng-href="[% edit(content.id, 'admin_'+content.content_type_name+'_show') %]" class="link">
                      <i class="fa fa-pencil"></i>
                      {t}Edit{/t}
                    </a>
                    <a class="link pointer" ng-click="restoreFromTrash(content)" type="button" title="{t}Restore{/t}">
                      <i class="fa fa-retweet"></i>
                      {t}Restore{/t}
                    </a>

                    <button class="link link-danger" ng-click="removePermanently(content)" type="button" title="{t}Restore{/t}">
                      <i class="fa fa-trash-o"></i>
                      {t}Remove permanently{/t}
                    </button>
                  </div>
                </td>
                <td class="center nowrap hidden-xs">
                  [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pagination-info pull-left">
          {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total %]
        </div>
        <div class="pull-right pagination-wrapper">
          <pagination class="no-margin" max-size="5" direction-links="true" ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total" num-pages="pagination.pages"></pagination>
        </div>
      </div>
    </div>
  </div>

  <script type="text/ng-template" id="modal-restore-from-trash">
    {include file="common/modals/_modalRestoreFromTrash.tpl"}
  </script>

  <script type="text/ng-template" id="modal-remove-permanently">
    {include file="common/modals/_modalRemovePermanently.tpl"}
  </script>

  <script type="text/ng-template" id="modal-batch-restore">
    {include file="common/modals/_modalBatchDelete.tpl"}
  </script>

  <script type="text/ng-template" id="modal-batch-remove-permanently">
    {include file="common/modals/_modalBatchRemovePermanently.tpl"}
  </script>

  <script type="text/ng-template" id="modal-remove-all">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        {t}Delete all trashed contents{/t}
    </h4>
  </div>
  <div class="modal-body">
      <p>{t escape=off}Are you sure you want to remove permanently all the contents inside the trash?{/t}</p>
  </div>
  <div class="modal-footer">
      <span class="loading" ng-if="deleting == 1"></span>
      <button class="btn btn-primary" ng-click="confirm()" type="button">{t}Yes, remove all{/t}</button>
      <button class="btn secondary" ng-click="close()" type="button">{t}No{/t}</button>
  </div>
  </script>
</div>
{/block}
