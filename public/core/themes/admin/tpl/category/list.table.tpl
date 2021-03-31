{extends file="common/extension/list.table.tpl"}

{block name="columns"}{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="400">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" width="200">
    {t}Slug{/t}
  </th>
  <th class="text-center v-align-middle" width="80">
    <i class="fa fa-picture-o"></i>
  </th>
  <th class="text-center v-align-middle" width="80">
    <i class="fa fa-paint-brush"></i>
  </th>
  <th class="text-center v-align-middle" width="100">
    {t}Contents{/t}
  </th>
  <th class="text-center v-align-middle" width="100">
    {t}Visible{/t}
  </th>
  <th class="text-center v-align-middle" width="100">
    {t}Enabled{/t}
  </th>
  <th class="text-center v-align-middle" width="100">
    {t}RSS{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle">
    <div class="[% 'm-l-' + 30 * levels[getItemId(item)] %]">
      [% item.title %]
      <div class="listing-inline-actions">
        <translator item="data.items[$index]" keys="data.extra.keys" link="[% routing.generate('backend_category_show', { id: getItemId(item) }) %]" ng-class="{ 'dropup': $index >= items.length - 1 }" ng-if="data.extra.locale.multilanguage && data.extra.locale.available" options="data.extra.locale" text="{t}Edit{/t}"></translator>
        <a class="btn btn-default btn-small" href="[% routing.generate('backend_category_show', { id: getItemId(item) }) %]" ng-if="!data.extra.locale.multilanguage || !data.extra.locale.available">
          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
        </a>
        <div uib-tooltip="{t}Only empty categories can be deleted{/t}" tooltip-enable="data.extra.stats[getItemId(item)] > 0" tooltip-class="tooltip-danger">
          <button class="btn btn-danger btn-small" ng-click="delete(getItemId(item))" ng-disabled="data.extra.stats[getItemId(item)] > 0" type="button">
            <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
          </button>
        </div>
        {acl isAllowed="MASTER"}
          <div class="btn-group" ng-class="{ 'dropup': $index >= items.length - 1 }">
            <button class="btn btn-white btn-small dropdown-toggle" data-toggle="dropdown" ng-disabled="isEmpty(item)" type="button">
              <i class="fa fa-ellipsis-h"></i>
            </button>
            <ul class="dropdown-menu no-padding">
              <li>
                <a href="#" ng-click="move(getItemId(item), item)">
                  <i class="fa fa-flip-horizontal fa-reply"></i>
                  {t}Move contents{/t}
                </a>
              </li>
              <li>
                <a href="#" ng-click="empty(getItemId(item))">
                  <i class="fa fa-fire"></i>
                  {t}Delete contents{/t}
                </a>
              </li>
            </ul>
          </div>
        {/acl}
      </div>
    </div>
  </td>
  <td class="v-align-middle">
    [% item.name %]
  </td>
  <td class="text-center v-align-middle">
    <dynamic-image class="img-thumbnail" instance="{$app.instance->getMediaShortPath()}/" ng-model="item.logo_path" only-image="true"></dynamic-image>
  </td>
  <td class="text-center v-align-middle">
    <span class="badge badge-white" ng-if="item.color" ng-style="{ 'background-color': item.color}">&nbsp;&nbsp;</span>
  </td>
  <td class="text-center v-align-middle">
    <span class="badge badge-default" ng-class="{ 'badge-danger': !data.extra.stats[getItemId(item)] || data.extra.stats[getItemId(item)] == 0 }">
      <strong>
        [% data.extra.stats[getItemId(item)] ? data.extra.stats[getItemId(item)] : 0 %]
      </strong>
    </span>
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'visible', item.visible != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.visibleLoading, 'fa-check text-success' : !item.visibleLoading && item.visible == '1', 'fa-times text-error': !item.visibleLoading && item.visible == '0' }"></i>
    </button>
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'enabled', item.enabled != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.enabledLoading, 'fa-check text-success' : !item.enabledLoading && item.enabled == '1', 'fa-times text-error': !item.enabledLoading && item.enabled == '0' }"></i>
    </button>
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'rss', item.rss != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.rssLoading, 'fa-check text-success' : !item.rssLoading && item.rss == '1', 'fa-times text-error': !item.rssLoading && item.rss == '0' }"></i>
    </button>
  </td>
{/block}
