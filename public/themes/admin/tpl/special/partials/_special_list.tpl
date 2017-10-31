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
        <li class="quicklinks hidden-xs ng-cloak"  ng-init="categories = {json_encode($categories)|clear_json}">
          <ui-select name="author" theme="select2" ng-model="criteria.pk_fk_content_category">
            <ui-select-match>
              <strong>{t}Category{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.value as item in categories | filter: { name: $select.search }">
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
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="criteria.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views  | filter: $select.search">
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

<div class="content" ng-init="init('special', 'backend_ws_contents_list')">
  {if $category == 'widget'}
  <div class="messages" ng-if="{$total_elements_widget} > 0 && total != {$total_elements_widget}">
    <div class="alert alert-info">
      <button class="close" data-dismiss="alert">×</button>
      {t 1=$total_elements_widget}You must put %1 specials in the HOME{/t}<br>
    </div>
  </div>
  {/if}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
        <div class="center">
          <h4>{t}Unable to find any special that matches your search.{/t}</h4>
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
              <th class="title">{t}Title{/t}</th>
              <th class="hidden-xs" width="200">{t}Section{/t}</th>
              {if $category!='widget'}
              {acl isAllowed="SPECIAL_HOME"}<th class="hidden-xs text-center" width="100">{t}Home{/t}</th>{/acl}
              {acl isAllowed="SPECIAL_FAVORITE"}<th class="hidden-xs text-center" width="100">{t}Favorite{/t}</th>{/acl}
              {/if}
              {acl isAllowed="SPECIAL_AVAILABLE"}<th class="text-center" width="100">{t}Published{/t}</th>{/acl}
            </tr>
          </thead>
          <tbody {if $category == 'widget'}ui-sortable ng-model="contents"{/if}>
            <tr ng-repeat="content in contents" ng-class="{ row_selected: isSelected(content.id) }">
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
                  {acl isAllowed="SPECIAL_UPDATE"}
                  <a class="link" href="[% edit(content.id, 'admin_special_show') %]">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  {/acl}
                  {acl isAllowed="SPECIAL_DELETE"}
                  <button class="del link link-danger" ng-click="sendToTrash(content)" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                  </button>
                  {/acl}
                </div>
              </td>
              <td class="hidden-xs">
                [% extra.categories[content.category] %]
              </td>
              {if $category!='widget'}
              {acl isAllowed="SPECIAL_HOME"}
                <td class="hidden-xs text-center">
                  <button class="btn btn-white" ng-if="content.author.meta.is_blog != 1" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_in_home', 'in_home', content.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.home_loading == 1, 'fa-home text-info': !content.home_loading && content.in_home == 1, 'fa-home': !content.home_loading && content.in_home == 0 }"></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="!content.home_loading && content.in_home == 0"></i>
                  </button>
                </td>
              {/acl}
              {acl isAllowed="SPECIAL_FAVORITE"}
                <td class="hidden-xs text-center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_toggle_favorite', 'favorite', content.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.favorite_loading == 1, 'fa-star text-warning': !content.favorite_loading && content.favorite == 1, 'fa-star-o': !content.favorite_loading && content.favorite == 0 }"></i>
                  </button>
                </td>
              {/acl}
              {/if}
              {acl isAllowed="SPECIAL_AVAILABLE"}
                <td class="text-center">
                  <button class="btn btn-white" ng-click="updateItem($index, content.id, 'backend_ws_content_set_content_status', 'content_status', content.content_status != 1 ? 1 : 0, 'loading')" type="button">
                    <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': content.loading == 1, 'fa-check text-success': !content.loading && content.content_status == 1, 'fa-times text-danger': !content.loading && content.content_status == 0 }"></i>
                  </button>
                </td>
              {/acl}
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
