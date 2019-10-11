<div class="grid simple">
{extends file="common/extension/list.table.tpl"}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="20">
    #
  </th>
  <th class="v-align-middle" width="400">
    {t}Name{/t}
  </th>
  <th class="text-center v-align-middle" width="150">
    {t}Synchronization{/t}
  </th>
  <th class="text-center v-align-middle" width="50">
    <i class="fa fa-paint-brush"></i>
  </th>
  <th class="text-center v-align-middle" width="110">
    {t}Automatic{/t}
  </th>
  <th class="text-center v-align-middle" width="50">
    {t}Activated{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle">
    [% getItemId(item) %]
  </td>
  <td class="v-align-middle">
    [% item.name %]
    <div class="listing-inline-actions m-t-10">
      <a class="btn btn-default btn-small" href="[% routing.generate(routes.redirect, { id: getItemId(item) }) %]">
        <i class="fa fa-pencil"></i>
        {t}Edit{/t}
      </a>
      <button class="btn btn-danger btn-small" ng-click="delete(getItemId(item))" type="button">
        <i class="fa fa-trash-o"></i>
        {t}Remove{/t}
      </button>
      {acl isAllowed="MASTER"}
        <div class="btn-group" ng-class="{ 'dropup': $index >= items.length - 1 }">
          <button class="btn btn-small btn-white dropdown-toggle" data-toggle="dropdown" type="button">
            <i class="fa fa-ellipsis-h"></i>
          </button>
          <ul class="dropdown-menu no-padding">
            <li>
              <a href="#" ng-click="clean(item)">
                <i class="fa fa-fire m-r-5"></i>
                {t}Clean files{/t}
              </a>
            </li>
            <li>
              <a href="#" ng-click="sync(item)">
                <i class="fa fa-retweet m-r-5"></i>
                {t}Sync{/t}
              </a>
            </li>
          </ul>
        </div>
      {/acl}
    </div>
  </td>
  <td class="text-center v-align-middle">
    <span class="badge badge-default text-bold text-uppercase">
      [% data.extra.sync_from[item.sync_from] %]
    </span>
  </td>
  <td class="text-center v-align-middle">
    <span class="badge badge-default" ng-style="{ 'background-color': item.color }" ng-show="item.color">
      &nbsp;&nbsp;
    </span>
  </td>
  <td class="text-center v-align-middle">
    <i class="fa" ng-class="{ 'fa-check text-success': item.auto_import == 1, 'fa-times text-danger': !item.auto_import || item.auto_import == 0 }"></i>
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'activated', item.activated != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated == 1, 'fa-times text-danger': !item.activatedLoading && !item.activated || item.activated == 0 }"></i>
    </button>
  </td>
{/block}
