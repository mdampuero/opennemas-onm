{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-controller="NewsAgencyServerListCtrl" ng-init="init('', {}, 'id', 'desc', 'backend_ws_news_agency_servers_list', '{{$smarty.const.CURRENT_LANGUAGE}}')">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-microphone fa-lg"></i>
                <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/788682-opennemas-agencias-de-noticias" target="_blank" tooltip="{t}Help{/t}" tooltip-placement="bottom">
                  <i class="fa fa-question"></i>
                </a>
                {t}News agency{/t}
              </h4>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h5>{t}Configuration{/t}</h5>
            </li>
          </ul>
        </div>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_news_agency}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <a class="btn btn-primary" href="{url name=backend_news_agency_server_new}">
                <i class="fa fa-plus"></i>
                {t}Add server{/t}
              </a>
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
              <h4>{t}No servers available{/t}</h4>
            </div>
          </div>
          <div class="table-wrapper ng-cloak" ng-if="!loading && contents.length > 0">
            <table id="source-list" class="table table-hover no-margin">
              <tr>
                <th>{t}Source name{/t}</th>
                <th class="center">{t}Sync from{/t}</th>
                <th class="center" style="width:1px">{t}Activated{/t}</th>
              </tr>
              <tr ng-repeat="item in contents">
                <td class="server_name">
                  [% item.name %]
                  <div class="listing-inline-actions">
                    <a class="link" href="[% edit(item.id, 'backend_news_agency_server_show') %]">
                      <i class="fa fa-pencil"></i>
                      {t}Edit{/t}
                    </a>
                    {acl isAllowed="ONLY_MASTERS"}
                      <button class="btn btn-link"  ng-click="clean($index, item.id)">
                        <i class="fa" ng-class="{ 'fa-fire': !item.cleaning, 'fa-circle-o-notch fa-spin': item.cleaning }"></i>
                        {t}Remove local files{/t}
                      </button>
                    {/acl}
                    <button class="link link-danger" ng-click="delete(item, 'backend_ws_news_agency_server_delete')" type="button">
                      <i class="fa fa-trash-o"></i>
                      {t}Remove{/t}
                    </button>
                  </div>
                </td>
                <td class="server_name nowrap center">
                  [% extra.sync_from[item.sync_from] %]
                </td>
                <td class="server_name center">
                  <button class="btn btn-white" ng-click="updateItem($index, item.id, 'backend_ws_news_agency_server_toggle', 'activated', item.activated != 1 ? 1 : 0, 'loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.loading, 'fa-check text-success' : !item.loading && item.activated == '1', 'fa-times text-error': !item.loading && item.activated == '0' }"></i>
                  </button>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="modal-delete">
    {include file="news_agency/modals/_modal_remove_config.tpl"}
  </script>
{/block}
