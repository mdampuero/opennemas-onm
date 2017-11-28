{extends file="base/admin.tpl"}

{block name="content"}
<div method="post" ng-controller="TrashListCtrl" ng-init="criteria = { epp: 10, in_litter: 1, page: 1 }; init('content', 'backend_ws_contents_list')">
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
              <button class="btn btn-danger" type="button" ng-click="removeAll()" id="remove-all-button">
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
            <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button" id="clear-selection-button">
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
            <button class="btn btn-link" ng-click="restoreFromTrashSelected()" uib-tooltip="{t}Restore{/t}" tooltip-placement="bottom" type="button" id="deselect_button">
              <i class="fa fa-retweet fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="removePermanentlySelected()" uib-tooltip="{t}Remove{/t}" tooltip-placement="bottom" type="button" id="remove_button">
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
            <input class="no-boarder" type="text" name="title" ng-model="criteria.title" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by name{/t}" />
          </li>
          <li class="quicklinks hidden-xs">
            <select id="content_type_name" ng-model="criteria.content_type_name" data-label="<strong>{t}Content Type{/t}</strong>" class="select2">
              <option value="">{t}All{/t}</option>

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
          <li class="quicklinks hidden-xs ng-cloak">
            <ui-select name="view" theme="select2" ng-model="criteria.epp">
              <ui-select-match>
                <strong>{t}View{/t}:</strong> [% $select.selected %]
              </ui-select-match>
              <ui-select-choices repeat="item in views | filter: $select.search">
                <div ng-bind-html="item | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </li>
          <li class="ng-cloak">
            <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-lg fa-refresh" ng-class="{ 'fa-spin': loading }"></i>
            </button>
          </li>
        </ul>
        <ul class="nav quick-section pull-right ng-cloak" ng-if="contents.length > 0">
          <li class="quicklinks hidden-xs">
            <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
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
                  <div class="small-text">
                    <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                  </div>
                  <div class="listing-inline-actions">
                    <a ng-if="content.content_type_name == 'static_page'" ng-href="[% edit(content.id, 'backend_'+content.content_type_name+'_show') %]" class="link">
                      <i class="fa fa-pencil"></i>
                      {t}Edit{/t}
                    </a>
                    <a ng-if="content.content_type_name != 'static_page'" ng-href="[% edit(content.id, 'admin_'+content.content_type_name+'_show') %]" class="link">
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
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="grid-footer clearfix ng-cloak" ng-if="!loading && contents.length > 0">
        <div class="pull-right">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
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
    {include file="common/modals/_modalBatchRestoreFromTrash.tpl"}
  </script>
  <script type="text/ng-template" id="modal-batch-remove-permanently">
    {include file="common/modals/_modalBatchRemovePermanently.tpl"}
  </script>

  <script type="text/ng-template" id="modal-remove-all">
    {include file="trash/modals/_modalTrashRemoveAll.tpl"}
  </script>
</div>
{/block}
