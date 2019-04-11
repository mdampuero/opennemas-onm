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
            <th class="hidden-xs hidden-sm" width=100></th>
            <th>{t}Title{/t}</th>
            {acl isAllowed="VIDEO_HOME"}
              <th class="hidden-xs text-center" width="100">{t}Home{/t}</th>
            {/acl}
            {acl isAllowed="VIDEO_FAVORITE"}
              <th class="hidden-xs text-center" width="100">{t}Favorite{/t}</th>
            {/acl}
            {acl isAllowed="VIDEO_AVAILABLE"}
              <th class="text-center" width="100">{t}Published{/t}</th>
            {/acl}
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
            <td class="hidden-sm hidden-xs">
              <div style="height: 120px; width: 120px;">
                <dynamic-image ng-if="item.thumb_image" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.thumb_image"></dynamic-image>
                <dynamic-image ng-if="!item.thumb_image" class="img-thumbnail" ng-model="item.thumb"></dynamic-image>
              </div>
            </td>
            <td>
              <div class="visible-xs visible-sm" style="max-height: 150px; max-width: 150px; ">
                <dynamic-image ng-if="item.thumb_image" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.thumb_image"></dynamic-image>
                <dynamic-image ng-if="!item.thumb_image" class="img-thumbnail" ng-model="item.thumb"></dynamic-image>
              </div>
              [% item.title %]
              <div class="small-text">
                <strong>{t}Created{/t}:</strong> [% item.created | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
              </div>
              <div class="listing-inline-actions">
                {acl isAllowed="VIDEO_UPDATE"}
                <a class="btn btn-default btn-small" href="[% routing.generate('backend_video_show', { id: getId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
                  <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                </a>
                <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_video_show', { id: getId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                {/acl}

                {acl isAllowed="VIDEO_DELETE"}
                <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
                  <i class="fa fa-trash-o m-r-5"></i> {t}Remove{/t}
                </button>
                {/acl}
              </div>
            </td>
            {acl isAllowed="VIDEO_HOME"}
              <td class="hidden-xs text-center">
                <button class="btn btn-white" ng-click="patch(item, 'in_home', item.in_home != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_homeLoading == 1, 'fa-home text-info': !item.in_homeLoading == 1 && item.in_home == 1, 'fa-home': !item.in_homeLoading == 1 && item.in_home == 0 }"></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="!item.in_homeLoading == 1 && item.in_home == 0"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="VIDEO_FAVORITE"}
              <td class="hidden-xs text-center">
                <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoritLoading == 1 && item.favorite == 1, 'fa-star-o': !item.favoriteLoading == 1 && item.favorite != 1 }"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="VIDEO_AVAILABLE"}
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
  <div class="grid-footer clearfix ng-cloak">
    <div class="pull-right">
      <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="data.total"></onm-pagination>
    </div>
  </div>
</div>
