{extends file="common/extension/list.tpl"}

{block name="icon"}
  <i class="fa fa-shield m-r-10"></i>
{/block}

{block name="title"}
  {t}Configuración 2FA{/t}
{/block}

{block name="selectedActions"}
   <li class="quicklinks">
    <button
      class="btn btn-link"
      ng-click="deleteSessionsSelected()"
      ng-disabled="bulkDeleteSessionLoading || bulkTwoFactorLoading"
      uib-tooltip="{t}Delete sessions{/t}"
      tooltip-placement="bottom"
      type="button">
      <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': bulkDeleteSessionLoading, 'fa-trash text-white': !bulkDeleteSessionLoading }"></i>
      <span class="m-l-5">{t}Borrar sesiones{/t}</span>
    </button>
  </li>
  <li class="quicklinks">
    <button
      class="btn btn-link"
      ng-click="toggleTwoFactorSelected(true)"
      ng-disabled="bulkTwoFactorLoading || bulkDeleteSessionLoading"
      uib-tooltip="{t}Enable 2FA{/t}"
      tooltip-placement="bottom"
      type="button">
      <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': bulkTwoFactorLoading, 'fa-check text-white': !bulkTwoFactorLoading }"></i>
      <span class="m-l-5">{t}Activar 2FA{/t}</span>
    </button>
  </li>
  <li class="quicklinks">
    <button
      class="btn btn-link"
      ng-click="toggleTwoFactorSelected(false)"
      ng-disabled="bulkTwoFactorLoading || bulkDeleteSessionLoading"
      uib-tooltip="{t}Disable 2FA{/t}"
      tooltip-placement="bottom"
      type="button">
      <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': bulkTwoFactorLoading, 'fa-times text-white': !bulkTwoFactorLoading }"></i>
      <span class="m-l-5">{t}Desactivar 2FA{/t}</span>
    </button>
  </li>
{/block}

{block name="leftFilters"}
  <li class="m-r-10 quicklinks">
    <div class="input-group input-group-animated">
      <span class="input-group-addon">
        <i class="fa fa-search fa-lg"></i>
      </span>
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.name }" name="name"
             ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="clear('name')" ng-show="criteria.name">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
{/block}

{block name="list"}
  {include file="security/twoFactor.table.tpl"}
{/block}