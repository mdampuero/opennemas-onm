<div class="grid simple ng-cloak" ng-show="!flags.http.loading && mode === 'list' && items.length > 0">
  <div class="grid-body no-padding">
    <div class="table-wrapper ng-cloak">
      <table class="table table-hover no-margin">
        <thead>
          <tr>
            <th class="checkbox-cell">
              <div class="checkbox checkbox-default">
                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
                <label for="select-all"></label>
              </div>
            </th>
            <th class="hidden-xs hidden-sm" style="width: 150px;"></th>
            <th class="title">{t}Information{/t}</th>
            <th class="hidden-xs" width="200">{t}Category{/t}</th>
            <th class="hidden-xs text-center" width="100">{t}Home{/t}</th>
            <th class="hidden-xs text-center" width="100">{t}Favorite{/t}</th>
            <th class="text-center" width="100">{t}Published{/t}</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }" data-id="[% item.id %]">
            <td class="checkbox-cell">
              <div class="checkbox check-default">
                <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                <label for="checkbox[%$index%]"></label>
              </div>
            </td>
            <td class="hidden-xs hidden-sm">
              <div ng-if="item.cover != ''" style="height: 120px; width: 120px;">
                <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover" transform="zoomcrop,220,220"></dynamic-image>
              </span>
              <div ng-if="item.cover == ''">
                <img ng-src="//placehold.it/80x60" class="thumbnail" />
              </span>
            </td>
            <td>
              <div ng-if="item.cover != ''" class="visible-xs visible-sm" style="height: 150px; width: 150px; margin: 0 auto 15px;">
                <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover"></dynamic-image>
              </div>
              [% item.title %]
              <div class="small-text">
                <strong>{t}Created{/t}:</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
              </div>
              <div class="listing-inline-actions">
                {acl isAllowed="ALBUM_UPDATE"}
                <a class="btn btn-default btn-small" href="[% routing.generate('backend_album_show', { id: getId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
                  <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                </a>
                <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_album_show', { id: getId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                {/acl}

                {acl isAllowed="ALBUM_DELETE"}
                <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
                  <i class="fa fa-trash-o m-r-5"></i> {t}Remove{/t}
                </button>
                {/acl}
              </div>
            </td>
            <td class="left hidden-xs">
              [% extra.categories[item.category].title %]
            </td>
            {acl isAllowed="ALBUM_HOME"}
              <td class="hidden-xs text-center">
                <button class="btn btn-white" ng-click="updateItem($index, item.id, 'backend_ws_content_toggle_in_home', 'in_home', item.in_home != 1 ? 1 : 0, 'home_loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.homeLoading == 1, 'fa-home text-info': !item.home_loading == 1 && item.in_home == 1, 'fa-home': !item.home_loading == 1 && item.in_home == 0 }"></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="!item.homeLoading == 1 && item.in_home == 0"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="ALBUM_FAVORITE"}
              <td class="hidden-xs text-center">
                <button class="btn btn-white"  ng-click="updateItem($index, item.id, 'backend_ws_content_toggle_favorite', 'favorite', item.favorite != 1 ? 1 : 0, 'favorite_loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favorite_loading == 1, 'fa-star text-warning': !item.favorite_loading == 1 && item.favorite == 1, 'fa-star-o': !item.favorite_loading == 1 && item.favorite != 1 }"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="ALBUM_AVAILABLE"}
              <td class="text-center">
                <button class="btn btn-white" ng-click="updateItem($index, item.id, 'backend_ws_content_set_content_status', 'content_status', item.content_status != 1 ? 1 : 0, 'loading')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.loading == 1, 'fa-check text-success': !item.loading == 1 && item.content_status == 1, 'fa-times text-danger': !item.loading == 1 && item.content_status == 0 }"></i>
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
