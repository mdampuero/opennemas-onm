{extends file="common/extension/list.table.tpl"}

{block name="columns"}{/block}

{block name="commonColumnsHeader"}
  <th class="v-align-middle" width="400">
      {t}Name{/t}
  </th>
  <th class="text-center v-align-middle" width="150">
    <i class="fa fa-eye" uib-tooltip="{t}Visibility{/t}" tooltip-placement="left"></i>
    <span ng-if="isHelpEnabled()">{t}Visibility{/t}</span>
  </th>
  <th class="text-center v-align-middle" width="150">
    <i class="fa fa-check" uib-tooltip="{t}Enabled{/t}" tooltip-placement="left"></i>
    <span ng-if="isHelpEnabled()">{t}Enabled{/t}</span>
  </th>
{/block}

{block name="commonColumnsBody"}
  <td class="v-align-middle">
    <div class="table-text">
      [% item.name %]
    </div>
    {block name="itemAction"}
      <div class="listing-inline-actions m-t-10 btn-group">
        <a class="btn btn-white btn-small" href="[% routing.generate('backend_user_group_show', { id: item.pk_user_group }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
          <i class="fa fa-pencil m-r-5"></i>
        </a>
        <button class="btn btn-white btn-small" ng-click="delete(item.pk_user_group)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
          <i class="fa fa-trash-o m-r-5 text-danger"></i>
        </button>
      </div>
    {/block}
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'private', item.private != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.privateLoading, 'fa-eye-slash text-error' : !item.privateLoading && item.private == 1, 'fa-eye text-success': !item.privateLoading && item.private == 0 }"></i>
    </button>
  </td>
  <td class="text-center v-align-middle">
    <button class="btn btn-white" ng-click="patch(item, 'enabled', item.enabled != 1 ? 1 : 0)" type="button">
      <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.enabledLoading, 'fa-check text-success' : !item.enabledLoading && item.enabled == 1, 'fa-times text-error': !item.enabledLoading && item.enabled == 0 }"></i>
    </button>
  </td>
{/block}
