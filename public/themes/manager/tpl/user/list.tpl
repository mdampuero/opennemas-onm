<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a ng-href="[% routing.ngGenerate('manager_users_list') %]">
              <i class="fa fa-user fa-lg"></i>
              {t}Users{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-primary" ng-href="[% routing.ngGenerate('manager_user_create') %]">
              <i class="fa fa-plus"></i>
              {t}Create{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{include file='common/selected_navbar.tpl' list="user"}
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="m-r-10 input-prepend inside search-form no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" ng-keyup="searchByKeypress($event)" ng-model="criteria.name_like[0].value" placeholder="Filter by name or username" type="text" style="width:250px;"/>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <ui-select ng-model="criteria.fk_user_group[0].value" theme="select2" >
            <ui-select-match>
              <strong>{t}Group{/t}:</strong> [% $select.selected.name %]
            </ui-select-match>
            <ui-select-choices repeat="item.id as item in template.flatGroups">
              <div ng-bind-html="item.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks hidden-xs">
          <ui-select name="view" ng-model="pagination.epp" theme="select2" >
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
          <button class="btn btn-white" ng-click="criteria = { name_like: [ { value: '', operator: 'like' } ], fk_user_group: [ { value: '' }] }; orderBy = [ { name: 'name', value: 'asc' } ]; pagination = { page: 1, epp: 25 }; refresh()">
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
	<div class="grid simple">
		<div class="grid-body no-padding">
			<div class="grid-overlay" ng-if="loading"></div>
      <div class="text-center" ng-if="items.length == 0">
       {t escape=off}There is no users created yet or <br/>your search don't match your criteria{/t}
     </div>
     <table class="table no-margin" ng-if="items.length > 0">
      <thead>
       <tr>
        <th style="width:15px;">
         <div class="checkbox checkbox-default">
          <input id="select-all" ng-model="selected.all" type="checkbox" ng-change="selectAll();">
          <label for="select-all"></label>
        </div>
      </th>
      <th class="left pointer" ng-click="sort('name')">
        {t}Full name{/t}
        <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
      </th>
      <th class="left pointer" ng-click="sort('username')">
        {t}Username{/t}
        <i ng-class="{ 'fa fa-caret-up': isOrderedBy('username') == 'asc', 'fa fa-caret-down': isOrderedBy('username') == 'desc'}"></i>
      </th>
      <th>{t}Group{/t}</th>
      <th class="text-center pointer" style="width: 10px;" ng-click="sort('activated')">{t}Activated{/t}</th>
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
    <td class="left">
     <a ng-href="[% routing.ngGenerate('manager_user_show', { id: item.id }); %]">
      [% item.name %]
    </a>
    <div class="listing-inline-actions">
      <a class="link" ng-href="[% routing.ngGenerate('manager_user_show', { id: item.id }); %]">
       <i class="fa fa-pencil"></i>{t}Edit{/t}
     </a>
     <button class="link link-danger" ng-click="delete(item)" type="button">
       <i class="fa fa-trash"></i>{t}Delete{/t}
     </button>
   </div>
 </td>
 <td class="left">
   [% item.username %]
 </td>
 <td class="left">
   <ul class="no-style">
    <li ng-repeat="id in item.id_user_group">
     [% template.groups[id].name %]
   </li>
 </ul>
</td>
<td class="text-center">
 <button class="btn btn-white" ng-click="setEnabled(item, item.activated == '1' ? '0' : '1')">
  <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.loading, 'fa-check text-success' : !item.loading && item.activated == '1', 'fa-times text-error': !item.loading && item.activated == '0' }"></i>
</button>
</td>
</tr>
</tbody>
<tfoot ng-if="items.length > 0">
  <tr>
    <td colspan="5">
      <div class="pagination-info pull-left" ng-if="items.length > 0">
        {t}Showing{/t} [% ((pagination.page - 1) * pagination.epp > 0) ? (pagination.page - 1) * pagination.epp : 1 %]-[% (pagination.page * pagination.epp) < pagination.total ? pagination.page * pagination.epp : pagination.total %] {t}of{/t} [% pagination.total|number %]
      </div>
      <div class="pull-right" ng-if="items.length > 0">
        <pagination class="no-margin" max-size="5" direction-links="true" items-per-page="pagination.epp" ng-model="pagination.page" total-items="pagination.total" num-pages="pagination.pages"></pagination>
      </div>
    </td>
  </tr>
</tfoot>
</table>
</div>
</div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
