<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_modules_list') %]">
              <i class="fa fa-plug"></i>
              {t}Modules{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a ng-href="[% routing.ngGenerate('manager_module_create') %]" class="btn btn-primary">
              <i class="fa fa-plus"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{include file='common/selected_navbar.tpl' list="extension"}
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="m-r-10 input-prepend inside search-input no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by UUID{/t}" ng-model="criteria.uuid_like[0].value" type="text" style="width:250px;"/>
        </li>
        <li class="quicklinks hidden-xs ng-cloak">
          <ui-select name="view" theme="select2" ng-model="pagination.epp">
            <ui-select-match>
              <strong>{t}View{/t}:</strong> [% $select.selected %]
            </ui-select-match>
            <ui-select-choices repeat="item in views | filter: $select.search">
              <div ng-bind-html="item | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
          </li>
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="list()">
            <i class="fa fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': loading, 'fa-repeat': !loading }"></i>
          </button>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        <li class="quicklinks toggle-columns">
          <div class="btn btn-link" ng-class="{ 'active': !columns.collapsed }" ng-click="toggleColumns()" tooltip-html="'{t}Columns{/t}'" tooltip-placement="left">
            <i class="fa fa-columns"></i>
          </div>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks form-inline pagination-links">
          <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content">
  <div class="row column-filters collapsed" ng-class="{ 'collapsed': columns.collapsed }">
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
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="table-wrapper">
        <div class="grid-overlay" ng-if="loading"></div>
        <table class="table table-hover no-margin">
          <thead ng-if="items.length >= 0">
            <tr>
              <th style="width:15px;">
                <div class="checkbox checkbox-default">
                  <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
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
              <th ng-show="isColumnEnabled('category')" width="150">
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
            <tr ng-if="items.length == 0">
              <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
            </tr>
            <tr ng-if="items.length >= 0" ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
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
                <a ng-href="[% item.show_url %]" title="{t}Edit{/t}">
                  [% item.name['en'] %]
                </a>
                <div class="listing-inline-actions">
                  <a class="link" ng-href="[% routing.ngGenerate('manager_module_show', { id: item.id }) %]" title="{t}Edit{/t}">
                    <i class="fa fa-pencil"></i>{t}Edit{/t}
                  </a>
                  <button class="link link-danger" ng-click="delete(item)" type="button">
                    <i class="fa fa-trash-o"></i>{t}Delete{/t}
                  </button>
                </div>
              </td>
              <td ng-show="isColumnEnabled('uuid')">
                [% item.uuid %]
              </td>
              <td ng-show="isColumnEnabled('category')">
                <span ng-if="item.metas.category === 'module'">{t}Module{/t}</span>
                <span ng-if="item.metas.category === 'pack'">{t}Pack{/t}</span>
                <span ng-if="item.metas.category === 'partner'">{t}Partner{/t}</span>
                <span ng-if="item.metas.category === 'service'">{t}Service{/t}</span>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('translations')">
                <span class="orb orb-success" ng-if="countStringsLeft(item) === 0" tooltip="{t}Translations completed{/t}">
                  <i class="fa fa-check""countStringsLeft(item) === 0"></i>
                </span>
                <span class="orb orb-danger" ng-if="countStringsLeft(item) > 0" tooltip="[% countStringsLeft(item) %] {t}translations left{/t}">
                  [% countStringsLeft(item) %]
                </span>
              </td>
              <td class="text-center" ng-show="isColumnEnabled('author')">
                [% item.author %]
              </td>
              <td class="text-center" ng-show="isColumnEnabled('price')">
                <div ng-repeat="price in item.metas.price">
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
                <button class="btn btn-white" type="button" ng-click="setEnabled(item, item.enabled == '1' ? '0' : '1')">
                  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.loading, 'fa-check text-success' : !item.loading &&item.enabled == '1', 'fa-times text-error': !item.loading && item.enabled == '0' }"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-right" ng-if="items.length > 0">
        <onm-pagination ng-model="pagination.page" items-per-page="pagination.epp" total-items="pagination.total"></onm-pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
