<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_modules_list') %]">
              <i class="fa fa-flip-horizontal fa-plug"></i>
              {t}Modules{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks" ng-if="security.hasPermission('EXTENSION_CREATE')">
            <a class="btn btn-success text-uppercase" ng-href="[% routing.ngGenerate('manager_module_create') %]">
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
        <li class="quicklinks" ng-if="security.hasPermission('EXTENSION_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('enabled', 0)" uib-tooltip="{t}Disabled{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-times fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('EXTENSION_UPDATE')">
          <button class="btn btn-link" ng-click="patchSelected('enabled', 1)" uib-tooltip="{t}Enabled{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-check fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('EXTENSION_UPDATE') && security.hasPermission('EXTENSION_DELETE')">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks" ng-if="security.hasPermission('EXTENSION_DELETE')">
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
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon">
              <i class="fa fa-search fa-lg"></i>
            </span>
            <input class="input-min-45 input-150" ng-class="{ 'dirty': criteria.uuid }" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by UUID{/t}" ng-model="criteria.uuid" type="text">
            <span class="input-group-addon input-group-addon-inside pointer no-animate ng-hide" ng-click="clear('uuid')" ng-show="criteria.uuid">
              <i class="fa fa-times"></i>
            </span>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="criteria.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-fire fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()" uib-tooltip="{t}Reload{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-lg fa-refresh" ng-class="{ 'fa-spin': loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="items">
  <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
    <i class="fa fa-7x fa-user-secret"></i>
    <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
  </div>
  <div class="grid simple" ng-if="items.length > 0">
    <div class="column-filters-toggle hidden-sm" ng-click="toggleColumns()" ng-if="items.length > 0"></div>
    <div class="row column-filters collapsed" ng-class="{ 'collapsed': columns.collapsed }" ng-if="items.length > 0">
      <div class="row">
        <div class="col-xs-12 title">
          <h5>{t}Columns{/t}</h5>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6 col-md-3 column">
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-image" checklist-model="columns.selected" checklist-value="'image'" type="checkbox">
              <label for="checkbox-image">
                {t}Image{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
              <label for="checkbox-name">
                {t}Name{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-uuid" checklist-model="columns.selected" checklist-value="'uuid'" type="checkbox">
              <label for="checkbox-uuid">
                {t}UUID{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-category" checklist-model="columns.selected" checklist-value="'category'" type="checkbox">
              <label for="checkbox-category">
                {t}Category{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-translations" checklist-model="columns.selected" checklist-value="'translations'" type="checkbox">
              <label for="checkbox-translations">
                {t}Translations{/t}
              </label>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 column">
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-author" checklist-model="columns.selected" checklist-value="'author'" type="checkbox">
              <label for="checkbox-author">
                {t}Author{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-price" checklist-model="columns.selected" checklist-value="'price'" type="checkbox">
              <label for="checkbox-price">
                {t}Price{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
              <label for="checkbox-created">
                {t}Created{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-updated" checklist-model="columns.selected" checklist-value="'updated'" type="checkbox">
              <label for="checkbox-updated">
                {t}Updated{/t}
              </label>
            </div>
          </div>
          <div>
            <div class="checkbox check-default">
              <input id="checkbox-enabled" checklist-model="columns.selected" checklist-value="'enabled'" type="checkbox">
              <label for="checkbox-enabled">
                {t}Enabled{/t}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
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
              <th class="pointer" ng-click="sort('id')" width="50">
                {t}#{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('id') == 'asc', 'fa fa-caret-down': isOrderedBy('id') == 'desc' }"></i>
              </th>
              <th class="text-center" ng-show="isColumnEnabled('image')" width="120">
                {t}Image{/t}
              </th>
              <th ng-show="isColumnEnabled('name')">
                {t}Name{/t}
              </th>
              <th class="pointer" ng-click="sort('uuid')" ng-show="isColumnEnabled('uuid')" width="250">
                {t}UUID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('uuid') == 'asc', 'fa fa-caret-down': isOrderedBy('uuid') == 'desc'}"></i>
              </th>
              <th class="text-center" ng-show="isColumnEnabled('category')" width="150">
                {t}Category{/t}
              </th>
              <th class="text-center" ng-show="isColumnEnabled('translations')" width="60">
                l10n
              </th>
              <th class="pointer text-center" ng-click="sort('author')" ng-show="isColumnEnabled('author')" width="250">
                {t}Author{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('author') == 'asc', 'fa fa-caret-down': isOrderedBy('author') == 'desc'}"></i>
              </th>
              <th class="text-center"  ng-show="isColumnEnabled('price')" width="150">
                {t}Price{/t}
              </th>
              <th class="pointer text-center" ng-click="sort('created')" ng-show="isColumnEnabled('created')" width="200">
                {t}Created{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('created') == 'asc', 'fa fa-caret-down': isOrderedBy('created') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('updated')" ng-show="isColumnEnabled('updated')" width="200">
                {t}Updated{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('updated') == 'asc', 'fa fa-caret-down': isOrderedBy('updated') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('enabled')" ng-show="isColumnEnabled('enabled')" width="50">
                {t}Enabled{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('enabled') == 'asc', 'fa fa-caret-down': isOrderedBy('enabled') == 'desc'}"></i>
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
              <td>
                [% item.id %]
              </td>
              <td ng-show="isColumnEnabled('image')">
                <dynamic-image class="img-thumbnail" path="[% item.images[0] %]" raw="true"></dynamic-image>
              </td>
              <td ng-show="isColumnEnabled('name')">
                [% item.name['en'] %]
                <div class="listing-inline-actions">
                  <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_module_show', { id: item.id }) %]" ng-if="security.hasPermission('EXTENSION_UPDATE')" title="{t}Edit{/t}">
                    <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                  </a>
                  <button class="btn btn-link text-danger" ng-click="delete(item.id)" ng-if="security.hasPermission('EXTENSION_DELETE')" type="button">
                    <i class="fa fa-trash- m-r-5"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td ng-show="isColumnEnabled('uuid')">
                [% item.uuid %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('category')">
                <span ng-if="item.category === 'module'">{t}Module{/t}</span>
                <span ng-if="item.category === 'pack'">{t}Pack{/t}</span>
                <span ng-if="item.category === 'partner'">{t}Partner{/t}</span>
                <span ng-if="item.category === 'service'">{t}Service{/t}</span>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('translations')">
                <span class="orb orb-success" ng-if="countStringsLeft(item) === 0" uib-tooltip="{t}Translations completed{/t}">
                  <i class="fa fa-check""countStringsLeft(item) === 0"></i>
                </span>
                <span class="orb orb-danger" ng-if="countStringsLeft(item) > 0" uib-tooltip="[% countStringsLeft(item) %] {t}translations left{/t}">
                  [% countStringsLeft(item) %]
                </span>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('author')">
                [% item.author %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('price')">
                <div ng-repeat="price in item.price">
                  [% price.value ? price.value : 0 %] €<span ng-if="price.type === 'monthly'">/{t}month{/t}</span><span ng-if="price.type === 'yearly'">/{t}year{/t}</span><span ng-if="price.type === 'item'">/{t}item{/t}</span>
                </div>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('created')">
                [% item.created %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('updated')">
                [% item.updated %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('enabled')">
                <button class="btn btn-white" ng-click="patch(item, 'enabled', item.enabled == '1' ? '0' : '1')" ng-if="security.hasPermission('EXTENSION_UPDATE')" type="button">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.enabledLoading, 'fa-check text-success' : !item.enabledLoading &&item.enabled == '1', 'fa-times text-error': !item.enabledLoading && item.enabled == '0' }"></i>
                </button>
                <span ng-if="!security.hasPermission('EXTENSION_UPDATE')">
                  <i class="fa" ng-class="{ 'fa-check text-success' : item.enabled == '1', 'fa-times text-error': item.enabled == '0' }"></i>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right">
        <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
