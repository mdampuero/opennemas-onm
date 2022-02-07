{extends file="common/extension/list.table.tpl"}

{block name="columns"}{/block}

{block name="commonColumnsHeader"}
  <th class="text-center v-align-middle" width="80">
    <i class="fa fa-picture-o"></i>
  </th>
  <th class="v-align-middle" width="400">
    {t}Name{/t}
  </th>
  <th class="v-align-middle" width="400">
    {t}Email{/t}
  </th>
  <th class="v-align-middle" width="200">
    {t}Lists{/t}
  </th>
  <th class="text-center v-align-middle" width="80">
    {t}Social{/t}
  </th>
  <th class="text-center v-align-middle" width="100">
    {t}Enabled{/t}
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="text-center v-align-middle">
    <dynamic-image class="img-thumbnail img-thumbnail-circle" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[item.avatar_img_id]" ng-if="item.avatar_img_id"></dynamic-image>
  </td>
  <td class="v-align-middle">
    <div class="table-text" ng-if="item.name">
      [% item.name %]
    </div>
    <small class="text-italic" ng-if="!item.name">
      {t}Unknown{/t}
    </small>
    {block name="itemActions"}
      <div class="listing-inline-actions m-t-10">
        <a class="btn btn-default btn-small" href="[% routing.generate('backend_subscriber_show', { id: item.id }) %]">
          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
        </a>
        <button class="btn btn-danger btn-small" ng-click="delete(item.id)" ng-if="backup.master || item.id != backup.id" type="button">
          <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
        </button>
      </div>
    {/block}
  </td>
  <td class="v-align-middle">
    <div class="table-text">
      [% item.email %]
    </div>
  </td>
  <td class="v-align-middle">
    <ul class="no-style">
      <li class="m-b-5 m-r-5 pull-left" ng-repeat="subscription in item.user_groups" ng-if="data.extra.subscriptions[subscription.user_group_id] && subscription.status !== 0" uib-tooltip="[% subscription.status === 2 ? '{t}Subscription requested{/t}' : '{t}Subscription disabled{/t}' %]" tooltip-enable="subscription.status === 2 || data.extra.subscriptions[subscription.user_group_id].enabled === 0">
        <a class="label text-uppercase no-animate" ng-class="{ 'label-danger': subscription.status !== 2 && !data.extra.subscriptions[subscription.user_group_id].enabled, 'label-default': subscription.status !== 2 && data.extra.subscriptions[subscription.user_group_id].enabled, 'label-warning': subscription.status === 2 }" href="[% routing.generate('backend_subscription_show', { id: subscription.user_group_id }) %]">
          <strong>[% data.extra.subscriptions[subscription.user_group_id].name %]</strong>
        </span>
        </a>
      </li>
    </ul>
  </td>
  <td class="text-center v-align-middle">
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
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="confirm('activated', item.activated != 1 ? 1 : 0, item)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.activatedLoading, 'fa-check text-success' : !item.activatedLoading && item.activated == '1', 'fa-times text-error': !item.activatedLoading && item.activated == '0' }"></i>
    </button>
  </td>
{/block}
