<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <i class="fa fa-plug"></i>
            {t}Modules{/t}
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a ng-href="[% routing.ngGenerate('manager_module_create') %]" class="btn btn-primary">
              <i class="fa fa-plus fa-lg"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{include file='common/selected_navbar.tpl' list="instance"}
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="m-r-10 input-prepend inside search-form no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" ng-keyup="searchByKeypress($event)" placeholder="{t}Filter by title{/t}" ng-model="criteria.title_like[0].value" type="text" style="width:250px;"/>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
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
        <li class="quicklinks hidden-xs">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-white" ng-click="criteria = {  title_like: [ { value: '', operator: 'like' } ]}; orderBy = [ { name: 'title', value: 'desc' } ]; pagination = { page: 1, epp: 25 }; refresh()">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <button class="btn btn-white" ng-click="refresh()">
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
          <div class="btn-group">
            <button class="btn btn-white" ng-click="pagination.page = pagination.page - 1" ng-disabled="pagination.page - 1 < 1" type="button">
              <i class="fa fa-chevron-left"></i>
            </button>
            <button class="btn btn-white" ng-click="pagination.page = pagination.page + 1" ng-disabled="pagination.page == pagination.pages" type="button">
              <i class="fa fa-chevron-right"></i>
            </button>
          </div>
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
            <input id="checkbox-name" checklist-model="columns.selected" checklist-value="'image'" type="checkbox">
            <label for="checkbox-name">
              {t}Image{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-domains" checklist-model="columns.selected" checklist-value="'name'" type="checkbox">
            <label for="checkbox-domains">
              {t}Name{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-domain-expire" checklist-model="columns.selected" checklist-value="'uuid'" type="checkbox">
            <label for="checkbox-domain-expire">
              {t}UUID{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-contact" checklist-model="columns.selected" checklist-value="'author'" type="checkbox">
            <label for="checkbox-contact">
              {t}Author{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-last-access" checklist-model="columns.selected" checklist-value="'created'" type="checkbox">
            <label for="checkbox-last-access">
              {t}Created{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-created" checklist-model="columns.selected" checklist-value="'updated'" type="checkbox">
            <label for="checkbox-created">
              {t}Updated{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-database" checklist-model="columns.selected" checklist-value="'database'" type="checkbox">
            <label for="checkbox-database">
              {t}Database{/t}
            </label>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 column">
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-contents" checklist-model="columns.selected" checklist-value="'contents'" type="checkbox">
            <label for="checkbox-contents">
              {t}Contents{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-articles" checklist-model="columns.selected" checklist-value="'articles'" type="checkbox">
            <label for="checkbox-articles">
              {t}Articles{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-opinions" checklist-model="columns.selected" checklist-value="'opinions'" type="checkbox">
            <label for="checkbox-opinions">
              {t}Opinions{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-advertisements" checklist-model="columns.selected" checklist-value="'advertisements'" type="checkbox">
            <label for="checkbox-advertisements">
              {t}Advertisements{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-albumns" checklist-model="columns.selected" checklist-value="'albumns'" type="checkbox">
            <label for="checkbox-albumns">
              {t}Albums{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-photos" checklist-model="columns.selected" checklist-value="'photos'" type="checkbox">
            <label for="checkbox-photos">
              {t}Photo{/t}
            </label>
          </div>
        </div>
        <div>
          <div class="checkbox check-default">
            <input id="checkbox-support" checklist-model="columns.selected" checklist-value="'support'" type="checkbox">
            <label for="checkbox-support">
              {t}Support plan{/t}
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
              <th class="pointer" ng-show="isEnabled('image')" width="120">
                {t}Image{/t}
              </th>
              <th class="pointer" ng-click="sort('instance')" ng-show="isEnabled('name')">
                {t}Name{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('instance') == 'asc', 'fa fa-caret-down': isOrderedBy('instance') == 'desc'}"></i>
              </th>
              <th class="pointer" ng-click="sort('title')" ng-show="isEnabled('uuid')" width="250">
                {t}UUID{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('title') == 'asc', 'fa fa-caret-down': isOrderedBy('title') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('end')" ng-show="isEnabled('author')" width="250">
                {t}Author{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('end') == 'asc', 'fa fa-caret-down': isOrderedBy('end') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('type')" ng-show="isEnabled('created')" width="250">
                {t}Created{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('type') == 'asc', 'fa fa-caret-down': isOrderedBy('type') == 'desc'}"></i>
              </th>
              <th class="pointer text-center" ng-click="sort('start')" ng-show="isEnabled('updated')" width="250">
                {t}Updated{/t}
                <i ng-class="{ 'fa fa-caret-up': isOrderedBy('start') == 'asc', 'fa fa-caret-down': isOrderedBy('start') == 'desc'}"></i>
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
              <td ng-show="isEnabled('image')">
                <dynamic-image class="img-thumbnail" path="[% item.images[0] %]" raw="true"></dynamic-image>
              </td>
              <td ng-show="isEnabled('name')">
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
              <td ng-show="isEnabled('uuid')">
                [% item.uuid %]
              </td>
              <td ng-show="isEnabled('author')">
                [% item.author %]
              </td>
              <td class="text-center" ng-show="isEnabled('created')">
                [% item.created %]
              </td>
              <td class="text-center" ng-show="isEnabled('updated')">
                [% item.updated %]
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="grid-footer clearfix">
      <div class="pull-left pagination-info" ng-if="items.length > 0">
        {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total|number %]
      </div>
      <div class="pull-right" ng-if="items.length > 0">
        <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="pagination.epp" ng-model="pagination.page" total-items="pagination.total" num-pages="pagination.pages"></pagination>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
