<div class="grid simple ng-cloak no-animate" ng-show="!flags.http.loading && app.mode === 'list' && items.length > 0">
  <div class="grid-body no-padding">
    <div class="table-wrapper ng-cloak">
      <table class="table table-fixed table-hover no-margin">
        <thead>
          <tr>
            <th class="text-center v-align-middle" width="50">
              <div class="checkbox checkbox-default">
                <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                <label for="select-all"></label>
              </div>
            </th>
            <th class="hidden-xs text-center v-align-middle" ng-if="isColumnEnabled('media')" width="80">
              <i class="fa fa-picture-o"></i>
            </th>
            <th class="v-align-middle" ng-if="isColumnEnabled('title')" width="400">
              {t}Title{/t}
            </th>
            <th class="text-center v-align-middle" ng-if="isColumnEnabled('created')" width="150">
              {t}Created{/t}
            </th>
            <th class="text-center v-align-middle" ng-if="isColumnEnabled('changed')" width="150">
              {t}Updated{/t}
            </th>
            <th class="text-center v-align-middle" ng-if="isColumnEnabled('starttime')" width="150">
              {t}Start date{/t}
            </th>
            <th class="text-center v-align-middle" ng-if="isColumnEnabled('endtime')" width="150">
              {t}End date{/t}
            </th>
            <th class="text-center v-align-middle" ng-if="isColumnEnabled('category')" width="200">
              {t}Category{/t}
            </th>
            <th class="v-align-middle" ng-if="isColumnEnabled('tags')" width="200">
              {t}Tags{/t}
            </th>
            <th class="text-center v-align-middle" ng-if="isColumnEnabled('author')" width="200">
              {t}Author{/t}
            </th>
            {acl isAllowed="ALBUM_HOME"}
              <th class="text-center v-align-middle visible-lg" ng-if="isColumnEnabled('home')" width="150">
                <i class="fa fa-home" ng-if="!isHelpEnabled()" uib-tooltip="{t}Home{/t}" tooltip-placement="left"></i>
                <span class="m-l-5" ng-if="isHelpEnabled()">{t}Home{/t}</span>
              </th>
            {/acl}
            {acl isAllowed="ALBUM_FAVORITE"}
              <th class="text-center v-align-middle visible-lg" ng-if="isColumnEnabled('favorite')" width="150">
                <i class="fa fa-star" ng-if="!isHelpEnabled()" uib-tooltip="{t}Favorite{/t}" tooltip-placement="left"></i>
                <span class="m-l-5" ng-if="isHelpEnabled()">{t}Favorite{/t}</span>
              </th>
            {/acl}
            {acl isAllowed="ALBUM_AVAILABLE"}
              <th class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')" width="150">
                <i class="fa fa-check" ng-if="!isHelpEnabled()" uib-tooltip="{t}Published{/t}" tooltip-placement="left"></i>
                <span class="m-l-5" ng-if="isHelpEnabled()">{t}Published{/t}</span>
              </th>
            {/acl}
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(getId(item)) }" data-id="[% getId(item) %]">
            <td class="text-center v-align-middle">
              <div class="checkbox check-default">
                <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="getId(item)" type="checkbox">
                <label for="checkbox[%$index%]"></label>
              </div>
            </td>
            <td class="hidden-xs" ng-if="isColumnEnabled('media')">
              <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover" transform="zoomcrop,220,220"></dynamic-image>
            </td>
            <td class="v-align-middle" ng-if="isColumnEnabled('title')">
              <div class="table-text" title="[% item.title %]">
                [% item.title %]
              </div>
              <div class="listing-inline-actions m-t-10">
                {acl isAllowed="ALBUM_UPDATE"}
                  <a class="btn btn-default btn-small" href="[% routing.generate('backend_album_show', { id: getId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_album_show', { id: getId(item) }) %]" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
                {/acl}
                {acl isAllowed="ALBUM_DELETE"}
                  <button class="btn btn-danger btn-small" ng-click="sendToTrash(item)" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Remove{/t}
                  </button>
                {/acl}
              </div>
            </td>
            <td class="text-center v-align-middle" ng-if="isColumnEnabled('created')">
              <div>
                <i class="fa fa-calendar"></i>
                [% item.created | moment : 'YYYY-MM-DD' %]
              </div>
              <small>
                <i class="fa fa-clock-o"></i>
                <strong>
                  [% item.created | moment : 'HH:mm:ss' %]
                </strong>
              </small>
            </td>
            <td class="text-center v-align-middle" ng-if="isColumnEnabled('changed')">
              <div>
                <i class="fa fa-calendar"></i>
                [% item.changed | moment : 'YYYY-MM-DD' %]
              </div>
              <small>
                <i class="fa fa-clock-o"></i>
                <strong>
                  [% item.changed | moment : 'HH:mm:ss' %]
                </strong>
              </small>
            </td>
            <td class="text-center v-align-middle" ng-if="isColumnEnabled('starttime')">
              <div>
                <i class="fa fa-calendar"></i>
                [% item.starttime | moment : 'YYYY-MM-DD' %]
              </div>
              <small>
                <i class="fa fa-clock-o"></i>
                <strong>
                  [% item.starttime | moment : 'HH:mm:ss' %]
                </strong>
              </small>
            </td>
            <td class="text-center v-align-middle" ng-if="isColumnEnabled('endtime')">
              <div ng-if="item.endtime">
                <i class="fa fa-calendar"></i>
                [% item.endtime | moment : 'YYYY-MM-DD' %]
              </div>
              <small ng-if="item.endtime">
                <i class="fa fa-clock-o"></i>
                <strong>
                  [% item.endtime | moment : 'HH:mm:ss' %]
                </strong>
              </small>
            </td>
            <td class="text-center v-align-middle" ng-if="isColumnEnabled('category')">
              <a class="label label-default m-r-5" href="[% routing.generate('backend_category_show', { id: data.extra.categories[item.category].pk_content_category })%]">
                <strong>
                  [% data.extra.categories[item.category].title %]
                </strong>
              </a>
            </td>
            <td class="v-align-middle" ng-if="isColumnEnabled('tags')">
              <span ng-if="!item.tags || item.tags.length === 0">{t}No tags{/t}</span>
              <a class="label label-default m-r-5" href="[% routing.generate('backend_tag_show', { id: data.extra.tags[id].id }) %]" ng-repeat="id in item.tags">
                <strong>
                  [% data.extra.tags[id].name %]
                </strong>
              </a>
            </td>
            <td class="v-align-middle" ng-if="isColumnEnabled('author')">
            </td>
            {acl isAllowed="ALBUM_HOME"}
              <td class="text-center v-align-middle visible-lg" ng-if="isColumnEnabled('home')">
                <button class="btn btn-white" ng-click="patch(item, 'in_home', item.in_home != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.in_homeLoading == 1, 'fa-home text-info': item.in_homeLoading !== 1 && item.in_home == 1, 'fa-home': !item.in_homeLoading == 1 && item.in_home == 0 }"></i>
                  <i class="fa fa-times fa-sub text-danger" ng-if="item.in_homeLoading !== 1 && item.in_home == 0"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="ALBUM_FAVORITE"}
              <td class="text-center v-align-middle visible-lg" ng-if="isColumnEnabled('favorite')">
                <button class="btn btn-white" ng-click="patch(item, 'favorite', item.favorite != 1 ? 1 : 0)" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.favoriteLoading == 1, 'fa-star text-warning': !item.favoriteLoading == 1 && item.favorite == 1, 'fa-star-o': !item.favoriteLoading == 1 && item.favorite != 1 }"></i>
                </button>
              </td>
            {/acl}
            {acl isAllowed="ALBUM_AVAILABLE"}
              <td class="text-center v-align-middle" ng-if="isColumnEnabled('content_status')">
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
