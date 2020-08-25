{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-controller="OpinionListCtrl" ng-init="forcedLocale = '{$locale}'; init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-quote-right m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <li class="quicklinks">
              <h4>{t}Opinions{/t}</h4>
            </li>
            <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks ng-cloak" ng-if="data.extra.locale.multilanguage && data.extra.locale.available">
              <translator keys="data.extra.keys" ng-model="config.locale.selected" options="data.extra.locale"></translator>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              {acl isAllowed="OPINION_SETTINGS"}
                <li class="quicklinks">
                  <a class="btn btn-link" href="{url name=backend_opinions_config}" title="{t}Config opinion module{/t}">
                    <i class="fa fa-cog fa-lg"></i>
                  </a>
                </li>
                <li class="quicklinks">
                  <span class="h-seperate"></span>
                </li>
              {/acl}
              {acl isAllowed="OPINION_CREATE"}
                <li class="quicklinks">
                  <a class="btn btn-success text-uppercase" href="{url name=backend_opinion_create}" title="{t}New opinion{/t}" id="create-button">
                    <i class="fa fa-plus"></i>
                    {t}Create{/t}
                  </a>
                </li>
              {/acl}
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section pull-left">
            <li class="quicklinks">
              <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right"type="button">
                <i class="fa fa-arrow-left fa-lg"></i>
              </button>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h4>
                [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
              </h4>
            </li>
          </ul>
          <ul class="nav quick-section pull-right">
            {acl isAllowed="CONTENT_OTHER_UPDATE"}
              {acl isAllowed="OPINION_AVAILABLE"}
                <li class="quicklinks">
                  <button class="btn btn-link" ng-click="patchSelected('content_status', 1)" uib-tooltip="{t}Publish{/t}" tooltip-placement="bottom" type="button">
                    <i class="fa fa-check fa-lg"></i>
                  </button>
                </li>
                <li class="quicklinks">
                  <button class="btn btn-link" href="#" ng-click="patchSelected('content_status', 0)" uib-tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
                    <i class="fa fa-times fa-lg"></i>
                  </button>
                </li>
                <li class="quicklinks">
                  <span class="h-seperate"></span>
                </li>
              {/acl}
              {acl isAllowed="OPINION_HOME"}
                <li class="quicklinks hidden-xs">
                  <a class="btn btn-link" href="#" ng-click="patchSelected('in_home', 1)" uib-tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
                    <i class="fa fa-home fa-lg"></i>
                  </a>
                </li>
                <li class="quicklinks hidden-xs">
                  <a class="btn btn-link" href="#" ng-click="patchSelected('in_home', 0)" uib-tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
                    <i class="fa fa-home fa-lg"></i>
                    <i class="fa fa-times fa-sub text-danger"></i>
                  </a>
                </li>
                <li class="quicklinks hidden-xs">
                  <span class="h-seperate"></span>
                </li>
              {/acl}
            {/acl}
            {acl isAllowed="OPINION_DELETE"}
              <li class="quicklinks">
                <a class="btn btn-link" href="#" ng-click="sendToTrash()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
                  <i class="fa fa-trash-o fa-lg"></i>
                </a>
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
            <li class="m-r-10 quicklinks">
              <div class="input-group input-group-animated">
                <span class="input-group-addon">
                  <i class="fa fa-search fa-lg"></i>
                </span>
                <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.title }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.title" placeholder="{t}Search{/t}" type="text">
                <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('title')" ng-show="criteria.title">
                  <i class="fa fa-times"></i>
                </span>
              </div>
            </li>
            <li class="hidden-xs m-r-10 ng-cloak quicklinks">
              {include file="ui/component/select/opinion_blog.tpl" label="true" ngModel="criteria.blog"}
            </li>
            <li class="hidden-xs ng-cloak m-r-10 quicklinks">
              {include file="ui/component/select/status.tpl" label="true" ngModel="criteria.content_status"}
            </li>
            <li class="hidden-xs hidden-sm ng-cloak m-r-10 quicklinks">
              {include file="ui/component/select/author.tpl" blog="true" label="true" ngModel="criteria.fk_author"}
            </li>
          </ul>
          <ul class="nav quick-section quick-section-fixed ng-cloak" ng-if="items.length > 0">
            <li class="quicklinks">
              <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && items.length == 0">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find any item that matches your search.{/t}</h3>
          <h4>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h4>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-if="!flags.http.loading && items.length > 0">
        <div class="grid-body no-padding">
          <div class="table-wrapper">
            <table class="table table-hover no-margin">
              <thead>
                <tr>
                  <th class="checkbox-cell">
                    <div class="checkbox checkbox-default">
                      <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                      <label for="select-all"></label>
                    </div>
                  </th>
                  <th>{t}Title{/t}</th>
                  <th class="hidden-xs">{t}Author{/t}</th>
                  <th class="text-center hidden-xs" width="100">{t}Home{/t}</th>
                  <th class="text-center hidden-xs" width="100">{t}Favorite{/t}</th>
                  <th class="text-center" width="100">{t}Published{/t}</th>
                </tr>
              </thead>
              <tbody>
                <tr ng-if="items.length > 0" ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                  <td class="checkbox-cell">
                    <div class="checkbox check-default">
                      <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                      <label for="checkbox[%$index%]"></label>
                    </div>
                  </td>
                  <td>
                    [% item.title %]
                    <div class="small-text">
                      <strong>{t}Created{/t}:</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                    </div>
                    <div class="small-text">
                      <span ng-if="item.starttime">
                        <strong>{t}Available from{/t} </strong>
                        [% item.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                      </span>
                      <span ng-if="item.endtime">
                        <strong>{t}to{/t} </strong> [% item.endtime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                      </span>
                    </div>
                    <div class="listing-inline-actions">
                      {acl isAllowed="OPINION_UPDATE"}
                      <a class="btn btn-default btn-small" href="[% routing.generate('backend_opinion_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
                        <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                      </a>
                      <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_opinion_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                      {/acl}

                      {acl isAllowed="OPINION_DELETE"}
                      <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
                        <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                      </button>
                      {/acl}
                    </div>
                  </td>
                  <td class="hidden-xs nowrap">
                    <span ng-if="item.fk_author">
                      <a href="[% routing.generate('backend_author_show', { id: item.fk_author }) %]">
                        [% (data.extra.authors | filter : { id: item.fk_author })[0].name %]
                        <span ng-if="(data.extra.authors | filter : { id: item.fk_author })[0].is_blog == 1">(Blog)</span>
                      </a>
                    </span>
                    <span ng-if="!item.fk_author || item.fk_author == 0">
                      [% item.author %]
                    </span>
                  </td>
                  <td class="text-center hidden-xs">
                    {acl isAllowed="OPINION_HOME"}
                    <button class="btn btn-white" ng-click="patch(item, 'in_home', item.in_home != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_homeLoading == 1, 'fa-home text-info': item.in_home == 1, 'fa-home': item.in_home == 0 }" ng-if="item.author.meta.is_blog != 1" ></i>
                      <i class="fa fa-times fa-sub text-danger" ng-if="!item.in_homeLoading && item.in_home == 0"></i>
                    </button>
                    <span ng-if="item.author.meta.is_blog == 1">
                      Blog
                    </span>
                    {/acl}
                  </td>
                  <td class="text-center hidden-xs">
                    {acl isAllowed="OPINION_FAVORITE"}
                    <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoriteLoading && item.favorite == 1, 'fa-star-o': !item.favoriteLoading && item.favorite != 1 }"></i>
                    </button>
                    {/acl}
                  </td>
                  <td class="text-center">
                    {acl isAllowed="OPINION_AVAILABLE"}
                    <button class="btn btn-white" {acl isNotAllowed="CONTENT_OTHER_UPDATE"} ng-if="item.fk_author == {$app.user->id}"{/acl} ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
                      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading && item.content_status == 0 }"></i>
                    </button>
                    {/acl}
                  </td>
                </tr>
                <tr ng-if="items.length == 0">
                  <td class="empty" colspan="11">
                    {t}There is no opinions yet.{/t}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="common/extension/modal.trash.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
      {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-update-selected">
      {include file="common/modals/_modalBatchUpdate.tpl"}
    </script>
  </div>
{/block}
