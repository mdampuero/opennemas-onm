<div class="grid simple ng-cloak" ng-show="!flags.http.loading && mode === 'list' && items.length > 0">
  <div class="grid-body no-padding">
    <div class="table-wrapper ng-cloak">
      <table class="table table-hover no-margin">
        <thead>
          <tr>
            <th class="checkbox-cell">
              <div class="checkbox checkbox-default">
                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                <label for="select-all"></label>
              </div>
            </th>
            <th></th>
            <th class="hidden-xs hidden-sm"></th>
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
            <td class="hidden-xs hidden-sm" style="height: 150px; width: 150px; margin: 0 auto 15px;">
              <div style="height: 120px; width: 120px;">
                <dynamic-image ng-show="item.cover != ''" autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover" transform="zoomcrop,220,220"></dynamic-image>
                <img ng-show="item.cover == ''" ng-src="//placehold.it/80x60" class="thumbnail" />
              </div>
            </td>
            <td>
              <div ng-show="item.cover != ''" class="visible-xs visible-sm text-left" style="width: 150px; margin: 0 auto 15px;">
                <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover"></dynamic-image>
              </div>
              [% item.title %]
              <div class="small-text m-t-5">
                <strong>{t}Created{/t}:</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
              </div>
              <div class="small-text">
                <strong>{t}Category{/t}:</strong> [% data.extra.categories[item.category].title %]
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
            {acl isAllowed="ALBUM_HOME"}
              <td class="hidden-xs text-center">
                <button class="btn btn-white" ng-click="patch(item, 'in_home', item.in_home != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_homeLoading == 1, 'fa-home text-info': item.in_homeLoading !== 1 && item.in_home == 1, 'fa-home': !item.in_homeLoading == 1 && item.in_home == 0 }"></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="item.in_homeLoading !== 1 && item.in_home == 0"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="ALBUM_FAVORITE"}
              <td class="hidden-xs text-center">
                <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoriteLoading == 1 && item.favorite == 1, 'fa-star-o': !item.favoriteLoading == 1 && item.favorite != 1 }"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="ALBUM_AVAILABLE"}
              <td class="text-center">
                <button class="btn btn-white" ng-click="patch(item, 'content_status', item.content_status != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.content_statusLoading == 1, 'fa-check text-success': !item.content_statusLoading == 1 && item.content_status == 1, 'fa-times text-danger': !item.content_statusLoading == 1 && item.content_status == 0 }"></i>
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
