{extends file="common/extension/list.table.tpl"}

{block name="commonColumns"}
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-picture" checklist-model="app.columns.selected" checklist-value="'picture'" type="checkbox">
    <label for="checkbox-picture">
      {t}Picture{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-name" checklist-model="app.columns.selected" checklist-value="'name'" type="checkbox">
    <label for="checkbox-name">
      {t}Name{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-email" checklist-model="app.columns.selected" checklist-value="'email'" type="checkbox">
    <label for="checkbox-email">
      {t}Email{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-username" checklist-model="app.columns.selected" checklist-value="'username'" type="checkbox">
    <label for="checkbox-username">
      {t}Username{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-usergroups" checklist-model="app.columns.selected" checklist-value="'usergroups'" type="checkbox">
    <label for="checkbox-usergroups">
     {t}User groups{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-social" checklist-model="app.columns.selected" checklist-value="'social'" type="checkbox">
    <label for="checkbox-social">
     {t}Social{/t}
    </label>
  </div>
  <div class="checkbox column-filters-checkbox">
    <input id="checkbox-enabled" checklist-model="app.columns.selected" checklist-value="'enabled'" type="checkbox">
    <label for="checkbox-enabled">
     {t}Enabled{/t}
    </label>
  </div>
{/block}

{block name="commonColumnsHeader"}
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('picture')" width="80">
    <i class="fa fa-picture-o"></i>
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('name')" width="400">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('email')" width="400">
    {t}Email{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('username')" width="240">
    {t}Username{/t}
  </th>
  <th class="v-align-middle" ng-if="isColumnEnabled('usergroups')" width="200">
    {t}User groups{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('social')" width="100">
    {t}Social{/t}
  </th>
  <th class="text-center v-align-middle" ng-if="isColumnEnabled('enabled')" width="100">
    {t}Enabled{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('picture')">
    <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.avatar_img_id].path"></dynamic-image>
  </td>
  <td ng-if="isColumnEnabled('name')">
    <div class="table-text" ng-if="item.name">
      [% item.name %]
    </div>
    <small class="text-italic" ng-if="!item.name">
      {t}Unknown{/t}
    </small>
    {block name="itemActions"}
      <div class="listing-inline-actions m-t-10 btn-group">
        <a class="btn btn-white btn-small" href="[% routing.generate('backend_user_show', { id: item.id }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
          <i class="fa fa-pencil text-success_"></i>
        </a>
        <button class="btn btn-white btn-small" ng-click="delete(item.id)" ng-if="backup.master || item.id != backup.id" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
          <i class="fa fa-trash-o text-danger"></i>
        </button>
      </div>
    {/block}
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('email')">
    <div class="table-text">
      [% item.email %]
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('username')">
    <div class="table-text">
      [% item.username %]
    </div>
  </td>
  <td class="v-align-middle" ng-if="isColumnEnabled('usergroups')">
    <ul class="no-style">
      <li class="m-b-5 m-r-5 pull-left" ng-repeat="(id, user_group) in item.user_groups" ng-if="data.extra.user_groups[user_group.user_group_id] && user_group.status !== 0" uib-tooltip="{t}User group disabled{/t}" tooltip-enable="data.extra.user_groups[user_group.user_group_id].enabled === 0">
        <a class="label text-uppercase" ng-class="{ 'label-danger': !data.extra.user_groups[id].enabled, 'label-default': data.extra.user_groups[user_group.user_group_id].enabled }" href="[% routing.generate('backend_user_group_show', { id: user_group.user_group_id }) %]">
          <strong>[% data.extra.user_groups[user_group.user_group_id].name %]</strong>
        </span>
        </a>
      </li>
    </ul>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('social')">
    <ul class="no-style">
      <li ng-show="item.facebook_id">
        <i class="fa fa-facebook-official fa-lg m-b-10 text-facebook"></i>
      </li>
      <li ng-show="item.google_id">
        <i class="fa fa-google-plus-official fa-lg m-b-10 text-google"></i>
      </li>
      <li ng-show="item.twitter_id">
        <i class="fa fa-twitter fa-lg m-b-5 text-twitter"></i>
      </li>
    </ul>
  </td>
  <td class="text-center v-align-middle" ng-if="isColumnEnabled('enabled')">
    <button class="btn btn-white" ng-click="confirm('activated', item.activated != 1 ? 1 : 0, item)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated == '1', 'fa-times text-error': !item.activatedLoading && item.activated == '0' }"></i>
    </button>
  </td>
{/block}
