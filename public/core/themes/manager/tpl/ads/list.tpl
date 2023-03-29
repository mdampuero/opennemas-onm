<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_ads_list') %]">
              <i class="fa fa-user"></i>
              {t}Ads.txt{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks" ng-if="security.hasPermission('ADS_CREATE')">
            <a class="btn btn-success text-uppercase text-white" ng-href="[% routing.ngGenerate('manager_ads_create') %]">
              <i class="fa fa-plus m-r-5"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section pull-left">
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="right" type="button">
            <i class="fa fa-arrow-left fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h4>
            [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
          </h4>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks" ng-if="security.hasPermission('ADS_DELETE')">
          <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="m-r-10 quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="input-min-45 input-200" ng-class="{ 'dirty': criteria.name }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.name" placeholder="{t}Search{/t}" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate" ng-click="clear('name')" ng-show="criteria.name">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="m-r-10 quicklinks">
          <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom">
            <i class="fa fa-fire fa-lg m-l-5 m-r-5"></i>
          </button>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-refresh fa-lg m-l-5 m-r-5" ng-class="{ 'fa-spin': loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right" ng-show="items && items.length > 0">
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="items">
  <div class="column-filters-toggle hidden-sm" ng-click="toggleColumns()" ng-if="items.length > 0"></div>
  <div class="column-filters collapsed hidden-sm" ng-class="{ 'collapsed': columns.collapsed }" ng-if="items.length > 0">
    <h5>{t}Columns{/t}</h5>
    <div class="row">
      <div class="col-md-3">
        <div class="checkbox check-default p-b-5">
          <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
          <label for="checkbox-name">
            {t}Name{/t}
          </label>
        </div>
        <div class="checkbox check-default p-b-5">
          <input id="checkbox-email" checklist-model="columns.selected" checklist-value="'instances'" type="checkbox">
          <label for="checkbox-email">
            {t}Instances{/t}
          </label>
        </div>
      </div>
    </div>
  </div>
  <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
    <i class="fa fa-7x fa-user-secret"></i>
    <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
  </div>
  <div class="grid simple" ng-if="items.length > 0">
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead>
            <tr>
              <th width="15">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="toggleAll();">
                  <label for="select-all"></label>
                </div>
              </th>
              <th class="pointer" ng-click="sort('name')" ng-show="isColumnEnabled('name')" width="300">
                {t}Name{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
              </th>
              <th class="text-center" ng-show="isColumnEnabled('instances')" width="300">
                {t}Instances{/t}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
              <td>
                <div class="checkbox check-default">
                  <input id="checkbox[%$index%]" checklist-model="selected.items" checklist-value="item.id" type="checkbox">
                  <label for="checkbox[%$index%]"></label>
                </div>
              </td>
              <td ng-show="isColumnEnabled('name')">
                [% item.name %]
                <div class="listing-inline-actions">
                  <a class="btn btn-default btn-small" ng-href="[% routing.ngGenerate('manager_ads_show', { id: item.id }) %]" ng-if="security.hasPermission('ADS_UPDATE')" title="{t}Edit{/t}">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn btn-danger btn-small" ng-click="delete(item)" ng-if="security.hasPermission('ADS_DELETE')" type="button">
                    <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td class="text-left" ng-show="isColumnEnabled('instances')">
                <div class="inline m-r-5 m-t-5 ng-scope" ng-repeat="instance in item.instances">
                  <a class="label label-defaul label-info text-bold ng-binding">
                      [% instance %]
                  </a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
