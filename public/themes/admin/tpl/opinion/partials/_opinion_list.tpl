<div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.contents.length == 0 }">
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
            [% selected.contents.length %] <span class="hidden-xs">{t}items selected{/t}</span>
          </h4>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        {acl isAllowed="CONTENT_OTHER_UPDATE"}
          {acl isAllowed="OPINION_AVAILABLE"}
            <li class="quicklinks">
              <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 1, 'loading')" uib-tooltip="{t}Publish{/t}" tooltip-placement="bottom">
                <i class="fa fa-check fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks">
              <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_set_content_status', 'content_status', 0, 'loading')" uib-tooltip="{t}Unpublish{/t}" tooltip-placement="bottom">
                <i class="fa fa-times fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
          {/acl}
          {acl isAllowed="OPINION_HOME"}
            <li class="quicklinks hidden-xs">
              <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 1, 'home_loading')" uib-tooltip="{t escape="off"}In home{/t}" tooltip-placement="bottom">
                <i class="fa fa-home fa-lg"></i>
              </a>
            </li>
            <li class="quicklinks hidden-xs">
              <a class="btn btn-link" href="#" ng-click="updateSelectedItems('backend_ws_contents_batch_toggle_in_home', 'in_home', 0, 'home_loading')" uib-tooltip="{t escape="off"}Drop from home{/t}" tooltip-placement="bottom">
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
            <a class="btn btn-link" href="#" ng-click="sendToTrashSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom">
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
        <li class="m-r-10 input-prepend inside search-input no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" name="title" ng-model="criteria.title" placeholder="{t}Search by title{/t}" type="text"/>
        </li>
        <li class="quicklinks hidden-xs">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs ng-cloak" ng-init="type = [ { name: '{t}All{/t}', value: null }, { name: '{t}Opinion{/t}', value: 0 }{is_module_activated name="BLOG_MANAGER"}, { name: '{t}Blog{/t}', value: 1 }{/is_module_activated} ]">
          <ui-select name="blog" theme="select2" ng-model="criteria.blog">
            <ui-select-match>
              <strong>{t}Type{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in type | filter: { name: $select.search }">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks hidden-xs ng-cloak" ng-init="status = [ { name: '{t}All{/t}', value: null }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ]">
          <ui-select name="status" theme="select2" ng-model="criteria.content_status">
            <ui-select-match>
              <strong>{t}Status{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks hidden-xs hidden-sm ng-cloak" ng-init="authors = {json_encode($authors)|clear_json}">
          <ui-select name="author" theme="select2" ng-model="criteria.fk_author">
            <ui-select-match>
              <strong>{t}Author{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in authors | filter: { name: $select.search }">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks hidden-sm hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="criteria.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
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
        <div class="text-center">
          <h4>{t}Unable to find any opinion that matches your search.{/t}</h4>
          <h6>{t}Maybe changing any filter could help or add one using the "Create" button above.{/t}</h6>
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
              <th>{t}Title{/t}</th>
              <th class="hidden-xs">{t}Author{/t}</th>
              <th class="text-center hidden-xs" width="100">{t}Home{/t}</th>
              <th class="text-center hidden-xs" width="100">{t}Favorite{/t}</th>
              <th class="text-center" width="100">{t}Published{/t}</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-if="contents.length > 0" ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
              <td class="checkbox-cell">
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.contents" checklist-value="content.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td>
                [% content.title %]
                <div class="small-text">
                  <strong>{t}Created{/t}:</strong> [% content.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
                </div>
                <div class="listing-inline-actions">
                  {acl isAllowed="OPINION_UPDATE"}
                  <a class="link" href="[% edit(content.id, 'admin_opinion_show') %]">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  {/acl}
                  {acl isAllowed="OPINION_DELETE"}
                  <button class="link link-danger" ng-click="sendToTrash(content)" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                  </button>
                  {/acl}
                </div>
              </td>
              <td class="hidden-xs nowrap">
                <span ng-if="content.fk_author && content.type_opinion == 0">
                  [% extra.authors[content.fk_author].name %] <span ng-if="extra.authors[content.fk_author].meta.is_blog == 1">(Blog)</span>
                </span>
                <span ng-if="!content.fk_author || content.fk_author == 0 || content.type_opinion != 0">
                  [% content.author %]
                </span>
              </td>
              <td class="text-center hidden-xs">
                {acl isAllowed="OPINION_HOME"}
                <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': content.in_home == 1, 'fa-home': content.in_home == 0 }" ng-if="content.author.meta.is_blog != 1" ></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading && content.in_home == 0"></i>
                </button>
                <span ng-if="content.author.meta.is_blog == 1">
                  Blog
                </span>
                {/acl}
              </td>
              <td class="text-center hidden-xs">
                {acl isAllowed="OPINION_FAVORITE"}
                <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" ng-if="content.type_opinion == 0" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite != 1 }"></i>
                </button>
                {/acl}
              </td>
              <td class="text-center">
                {acl isAllowed="OPINION_AVAILABLE"}
                <button class="btn btn-white" {acl isNotAllowed="CONTENT_OTHER_UPDATE"} ng-if="content.fk_author == {$app.user->id}"{/acl} ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
                </button>
                {/acl}
              </td>
            </tr>
            <tr ng-if="contents.length == 0">
              <td class="empty" colspan="11">
                {t}There is no opinions yet.{/t}
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
