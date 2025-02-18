<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_onmai_config') %]">
              <i class="fa fa-terminal"></i>
              {t}Prompts{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks m-r-10">
            <a class="btn btn-white" ng-click="openImportModal()">
              <span class="fa fa-sign-in"></span>
              {t}Import{/t}
            </a>
          </li>
          <li class="quicklinks m-r-10">
            <a class="btn btn-white" ng-href="{url name=manager_ws_onmai_prompt_config_download}?token=[% security.token %]">
              <span class="fa fa-download"></span>
              {t}Download{/t}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <uib-tabset class="tab-form">
    <uib-tab heading="{t}Prompts{/t}" ng-click="selectTab('prompts')">
      <ng-container>
        <div class="filters-navbar m-b-15">
          <div class="row">
            <div class="col-sm-6">
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
            </div>
            <div class="col-sm-6">
              <ul class="nav quick-section pull-right">
                <li class="quicklinks form-inline pagination-links">
                  <onm-pagination ng-model="criteria.page" items-per-page="criteria.epp" total-items="total"></onm-pagination>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks" ng-if="security.hasPermission('PROMPT_CREATE')">
                  <a class="btn btn-success text-uppercase text-white" ng-href="[% routing.ngGenerate('manager_onmai_prompt_create') %]">
                    <i class="fa fa-plus m-r-5"></i>
                    {t}Create{/t}
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
          <i class="fa fa-7x fa-user-secret"></i>
          <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
        </div>
        <div class="grid simple m-b-0" ng-if="items.length > 0">
          <div class="grid-body no-padding">
            <div class="table-wrapper">
              <div class="grid-overlay" ng-if="loading"></div>
              <table class="table table-hover no-margin">
                <thead>
                  <tr>
                    <th class="pointer" ng-click="sort('name')" width="300">
                      {t}Name{/t}
                      <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
                    </th>
                    <th >
                      {t}Mode{/t}
                    </th>
                    <th>
                      {t}Field{/t}
                    </th>
                    <th class="text-center" width="300">
                      {t}Instances{/t}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                    <td>
                      [% item.name %]
                      <div class="listing-inline-actions">
                        <a class="btn btn-default btn-small" ng-href="[% routing.ngGenerate('manager_onmai_prompt_show', { id: item.id }) %]" ng-if="security.hasPermission('PROMPT_UPDATE')" title="{t}Edit{/t}">
                          <i class="fa fa-pencil m-r-5"></i>{t}Edit{/t}
                        </a>
                        <button class="btn btn-danger btn-small" ng-click="delete(item)" ng-if="security.hasPermission('PROMPT_DELETE')" type="button">
                          <i class="fa fa-trash-o m-r-5"></i>{t}Delete{/t}
                        </button>
                      </div>
                    </td>
                    <td>
                      [% item.mode %]
                    </td>
                    <td>
                      [% item.field %]
                    </td>
                    <td class="text-center">
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
      </ng-container>
    </uib-tab>
    <uib-tab heading="{t}Roles{/t}" ng-click="selectTab('roles')">
      <ng-container>
        <div class="ng-cloak">
          <div class="m-b-20">
              <button class="btn btn-loading btn-success text-uppercase pull-right" ng-click="save()" ng-disabled="promptForm.$invalid || saving">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
              </button>
              <div class="clearfix"></div>
          </div>
          <div class="form-group">
            <div class="controls">
              <div class="row" ng-repeat="role in settings.onmai_roles track by $index">
                <div class="col-lg-4 col-md-3">
                  <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required maxlength="64">
                </div>
                <div class="col-lg-7 col-md-7">
                  <input class="form-control" ng-model="role.prompt" placeholder="{t}Prompt{/t}" type="text" required maxlength="2048">
                </div>
                <div class="col-lg-1 col-md-2 m-b-15">
                  <button class="btn btn-block btn-danger ng-cloak" ng-click="removeRole($index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addRole()" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
      </ng-container>
    </uib-tab>
    <uib-tab heading="{t}Tones{/t}" ng-click="selectTab('tones')">
      <ng-container>
        <div class="ng-cloak">
          <div class="m-b-20">
              <button class="btn btn-loading btn-success text-uppercase pull-right" ng-click="save()" ng-disabled="promptForm.$invalid || saving">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
              </button>
              <div class="clearfix"></div>
          </div>
          <div class="form-group">
            <div class="controls">
              <div class="row" ng-repeat="role in settings.onmai_tones track by $index">
                <div class="col-lg-4 col-md-3">
                  <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required maxlength="64">
                </div>
                <div class="col-lg-7 col-md-7">
                  <input class="form-control" ng-model="role.description" placeholder="{t}Description{/t}" type="text" required maxlength="2048">
                </div>
                <div class="col-lg-1 col-md-2 m-b-15">
                  <button class="btn btn-block btn-danger ng-cloak" ng-click="removeTone($index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addTone()" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
      </ng-container>
    </uib-tab>
    <uib-tab heading="{t}Instructions{/t}" ng-click="selectTab('instructions')">
      <ng-container>
        <div class="ng-cloak">
          <div class="m-b-20">
              <button class="btn btn-loading btn-success text-uppercase pull-right" ng-click="save()" ng-disabled="promptForm.$invalid || saving">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
              </button>
              <div class="clearfix"></div>
          </div>
          <div class="form-group">
            <div class="controls">
              <div class="row" ng-repeat="role in settings.onmai_instructions track by $index">
                <div class="col-lg-2 col-md-3 m-b-15">
                  <ui-select name="mode" class="form-control" theme="select2" ng-model="role.type" search-enabled="false" required ng-init="options = [ { name: '{t}Both{/t}', key: 'Both'},{ name: '{t}Create{/t}', key: 'New'}, { name: '{t}Edit{/t}', key: 'Edit'} ]">
                    <ui-select-match>
                      [% $select.selected.name %]
                    </ui-select-match>
                    <ui-select-choices repeat="item.key as item in options | filter: $select.search">
                      <div ng-bind-html="item.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>
                <div class="col-lg-2 col-md-3 m-b-15">
                  <ui-select name="field" class="form-control" theme="select2" ng-model="role.field" search-enabled="false" required ng-init="options = [ { name: '{t}All{/t}', key: 'all'}, { name: '{t}Titles{/t}', key: 'titles'}, { name: '{t}Introductions{/t}', key: 'introductions'}, { name: '{t}Bodies{/t}', key: 'bodies' } ]">
                    <ui-select-match>
                      [% $select.selected.name %]
                    </ui-select-match>
                    <ui-select-choices repeat="item.key as item in options | filter: $select.search">
                      <div ng-bind-html="item.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>
                <div class="col-lg-7 col-md-7">
                  <input class="form-control" ng-model="role.value" placeholder="{t}Instruction{/t}" type="text" required maxlength="2048">
                </div>
                <div class="col-lg-1 col-md-2 m-b-15">
                  <button class="btn btn-block btn-danger ng-cloak" ng-click="removeInstruction($index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
              <button class="btn btn-block btn-default" ng-click="addInstruction()" type="button">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
      </ng-container>
    </uib-tab>
  </uib-tabset>
</div>
<script type="text/ng-template" id="modal-import-settings">
  {include file="common/modalImportSettings.tpl"}
</script>
