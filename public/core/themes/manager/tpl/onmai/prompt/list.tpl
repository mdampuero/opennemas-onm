<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_onmai_prompt_list') %]">
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
                <li class="hidden-xs m-r-10 ng-cloak quicklinks">
                  <select class="form-control form-control-lg" ng-model="criteria.model">
                    <option value="">{t}Model{/t}: {t}Any{/t}</option>
                    <option value="[% item.id %]" ng-repeat="item in extra.onmai_models">[% item.title %]</option>
                  </select>
                </li>
                <li class="hidden-xs m-r-10 ng-cloak quicklinks">
                  <select class="form-control form-control-lg" ng-model="criteria.mode">
                    <option value="">{t}Mode{/t}: {t}Any{/t}</option>
                    <option value="New">{t}Create{/t}</option>
                    <option value="Edit">{t}Edit{/t}</option>
                    <option value="Agency">{t}Agency{/t}</option>
                  </select>
                </li>
                <li class="hidden-xs m-r-10 ng-cloak quicklinks">
                  <select class="form-control form-control-lg" ng-model="criteria.field">
                    <option value="">{t}Field{/t}: {t}Any{/t}</option>
                    <option value="titles">{t}Titles{/t}</option>
                    <option value="descriptions">{t}Descriptions{/t}</option>
                    <option value="bodies">{t}Bodies{/t}</option>
                  </select>
                </li>
                <li class="m-r-10 quicklinks">
                  <button class="btn btn-link" ng-click="resetFilters()" uib-tooltip="{t}Reset filters{/t}" tooltip-placement="bottom">
                    <i class="fa fa-fire fa-lg m-l-5 m-r-5"></i>
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
                    <th width="300" ng-click="sort('name')" class="pointer">
                      {t}Name{/t}
                      <i ng-class="{ 'fa fa-caret-up': isOrderedBy('name') == 'asc', 'fa fa-caret-down': isOrderedBy('name') == 'desc'}"></i>
                    </th>
                    <th ng-click="sort('model')" class="pointer">
                      {t}Model{/t}
                      <i ng-class="{ 'fa fa-caret-up': isOrderedBy('model') == 'asc', 'fa fa-caret-down': isOrderedBy('model') == 'desc'}"></i>
                    </th>
                    <th ng-click="sort('mode')" class="pointer">
                      {t}Mode{/t}
                      <i ng-class="{ 'fa fa-caret-up': isOrderedBy('mode') == 'asc', 'fa fa-caret-down': isOrderedBy('mode') == 'desc'}"></i>
                    </th>
                    <th ng-click="sort('field')" class="pointer">
                      {t}Field{/t}
                      <i ng-class="{ 'fa fa-caret-up': isOrderedBy('field') == 'asc', 'fa fa-caret-down': isOrderedBy('field') == 'desc'}"></i>
                    </th>
                    <th ng-click="sort('tone')" class="pointer">
                      {t}Tone{/t}
                      <i ng-class="{ 'fa fa-caret-up': isOrderedBy('tone') == 'asc', 'fa fa-caret-down': isOrderedBy('tone') == 'desc'}"></i>
                    </th>
                    <th ng-click="sort('role')" class="pointer">
                      {t}Role{/t}
                      <i ng-class="{ 'fa fa-caret-up': isOrderedBy('role') == 'asc', 'fa fa-caret-down': isOrderedBy('role') == 'desc'}"></i>
                    </th>
                    <th class="text-center" width="300">
                      {t}Instances{/t}
                    </th>
                    <th class="text-center" width="300">
                      {t}Published{/t}
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
                      <ng-container ng-if="item.model == null"><i>{t}Default model{/t}</i></ng-container>
                      <ng-container ng-if="item.model != null">[% item.model %]</ng-container>
                    </td>
                    <td>
                      [% item.mode %]
                    </td>
                    <td>
                      <ng-container ng-if="item.mode == 'Agency'"></ng-container>
                      <ng-container ng-if="item.mode != 'Agency'">[% item.field %]</ng-container>
                    </td>
                    <td>
                      [% item.tone %]
                    </td>
                    <td>
                      [% item.role %]
                    </td>
                    <td class="text-center">
                      <div class="inline m-r-5 m-t-5 ng-scope" ng-repeat="instance in item.instances">
                        <a class="label label-defaul label-info text-bold ng-binding">
                            [% instance %]
                        </a>
                      </div>
                    </td>
                    <td class="text-center v-align-middle" >
                      <button class="btn btn-white" ng-click="patch(item, 'status', item.status != 1 ? 1 : 0)" type="button">
                        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.statusLoading == 1, 'fa-check text-success': !item.statusLoading == 1 && item.status == 1, 'fa-times text-danger': !item.statusLoading == 1 && item.status == 0 }"></i>
                      </button>
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
        <div class="ng-cloak p-b-50">
          <div class="m-b-20">
              <button class="btn btn-loading btn-success text-uppercase pull-right" ng-click="save()" ng-disabled="promptForm.$invalid || saving">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
              </button>
              <div class="clearfix"></div>
          </div>
          <div class="form-group">
            <div class="controls">
              <div class="row" ng-repeat="role in settings.onmai_roles track by $index">
                <div class="col-lg-3 col-md-3">
                  <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required maxlength="64">
                </div>
                <div class="col-lg-2 col-md-3">
                  <ui-select name="mode" class="form-control" theme="select2" ng-model="role.field" search-enabled="false" required ng-init="options = [ { name: '{t}Titles{/t}', key: 'titles'}, { name: '{t}Descriptions{/t}', key: 'descriptions'}, { name: '{t}Bodies{/t}', key: 'bodies' }, { name: '{t}Agency{/t}', key: 'Agency' } ]">
                    <ui-select-match>
                      [% $select.selected.name %]
                    </ui-select-match>
                    <ui-select-choices repeat="item.key as item in options | filter: $select.search">
                      <div ng-bind-html="item.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>
                <div class="col-lg-6 col-md-6">
                  <input class="form-control" ng-model="role.prompt" placeholder="{t}Prompt{/t}" type="text" required maxlength="2048">
                </div>
                <div class="col-lg-1 col-md-1 m-b-15">
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
        <div class="ng-cloak p-b-50">
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
        <div class="ng-cloak p-b-50">
          <div class="m-b-20">
              <button class="btn btn-loading btn-success text-uppercase pull-right" ng-click="save()" ng-disabled="promptForm.$invalid || saving">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
              </button>
              <div class="clearfix"></div>
          </div>
          <div class="form-group">
            <div class="controls">
              <div class="row" ng-repeat="item in settings.onmai_instructions track by $index">
                <div class="col-md-3">
                  <input class="form-control" ng-model="item.title" placeholder="{t}Title{/t}" type="text" required maxlength="32">
                </div>
                <div class="col-md-7">
                  <input class="form-control" ng-model="item.value" placeholder="{t}Instruction{/t}" type="text" required maxlength="2048">
                </div>
                <div class="col-md-2 m-b-15 text-right">
                  <button class="btn btn-white" ng-click="activeInstruction($index)" type="button">
                      <i class="fa" ng-class="{
                          'fa-times text-danger': item.disabled == 1 && item.disabled != undefined,
                          'fa-check text-success': item.disabled != 1 || item.disabled == undefined
                      }"></i>
                  </button>
                  <button class="btn btn-danger ng-cloak" ng-click="removeInstruction($index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button>
                  {* <button class="btn btn-danger ng-cloak" ng-click="removeInstruction($index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button> *}
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
