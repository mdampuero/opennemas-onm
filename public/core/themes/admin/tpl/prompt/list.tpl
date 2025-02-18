{extends file="base/admin.tpl"}

{block name="content"}
<ng-container ng-controller="PromptListCtrl" ng-init="init()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="[% routing.generate('backend_onmai_config') %]">
                <i class="fa fa-terminal m-r-10"></i>
                {t}Prompts{/t}
              </a>
            </h4>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content" ng-cloak>
    <uib-tabset class="tab-form">
      <uib-tab heading="{t}Prompts{/t}" ng-click="selectTab('prompts')">
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
                  {acl isAllowed="PROMPT_CREATE"}
                    <li class="quicklinks">
                      <a class="btn btn-loading btn-success text-uppercase" href="[% routing.generate('backend_onmai_prompt_create') %]">
                        <i class="fa fa-plus m-r-5"></i>
                        {t}Create{/t}
                      </a>
                    </li>
                  {/acl}
                </ul>
              </div>
            </div>
          </div>
          <div class="grid simple m-b-0" ng-if="items.length > 0">
            <div class="grid-body no-padding">
              <div class="table-wrapper">
                <div class="grid-overlay" ng-if="loading"></div>
                <table class="table table-hover no-margin">
                  <thead>
                    <tr>
                      <th class="v-align-middle" width="500">
                        {t}Title{/t}
                      </th>
                      <th class="v-align-middle">
                        {t}Field{/t}
                      </th>
                      <th class="v-align-middle">
                        {t}Mode{/t}
                      </th>
                      <th class="v-align-middle">
                        {t}Default tone{/t}
                      </th>
                      <th class="v-align-middle">
                        {t}Default role{/t}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="item in items" ng-class="{ row_selected: isSelected(item.id) }">
                      <td class="v-align-middle">
                        <div class="table-text">
                          [% item.name %]
                        </div>
                        <div class="listing-inline-actions btn-group" ng-if="!item.instances">
                          <a class="btn btn-white btn-small" href="[% routing.generate('backend_onmai_prompt_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}" tooltip-placement="top">
                            <i class="fa fa-pencil text-success_"></i>
                          </a>
                          <a class="btn btn-white btn-small" ng-click="delete(item.id)" type="button" uib-tooltip="{t}Delete{/t}" tooltip-placement="top">
                            <i class="fa fa-trash-o text-danger"></i>
                          </a>
                        </div>
                        <div class="listing-inline-actions btn-group" ng-if="item.instances">
                          <small><i class="text-muted">[{t}System prompts{/t}]</i></small>
                        </div>
                      </td>
                      <td class="v-align-middle">
                        <div class="table-text">
                          [% item.field %]
                        </div>
                      </td>
                      <td class="v-align-middle">
                        <div class="table-text">
                          [% item.mode %]
                        </div>
                      </td>
                      <td class="v-align-middle">
                        <div class="table-text">
                          [% item.tone %]
                        </div>
                      </td>
                      <td class="v-align-middle">
                        <div class="table-text">
                          [% item.role %]
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
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
                <div class="row" ng-repeat="role in data.extra.roles track by $index">
                  <div class="col-lg-4 col-md-3">
                    <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required ng-disabled="role.readOnly" maxlength="64">
                  </div>
                  <div class="col-lg-7 col-md-7">
                    <input class="form-control" ng-model="role.prompt" placeholder="{t}Prompt{/t}" type="text" required ng-disabled="role.readOnly" maxlength="2048">
                  </div>
                  <div class="col-lg-1 col-md-2 m-b-15">
                    <button class="btn btn-block btn-danger ng-cloak" ng-click="removeRole($index)" type="button" ng-disabled="role.readOnly">
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
                  <div class="row" ng-repeat="role in data.extra.tones track by $index">
                    <div class="col-lg-4 col-md-3">
                      <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required ng-disabled="role.readOnly" maxlength="64">
                    </div>
                    <div class="col-lg-7 col-md-7">
                      <input class="form-control" ng-model="role.description" placeholder="{t}Description{/t}" type="text" required ng-disabled="role.readOnly" maxlength="2048">
                    </div>
                    <div class="col-lg-1 col-md-2 m-b-15">
                      <button class="btn btn-block btn-danger ng-cloak" ng-click="removeTone($index)" type="button" ng-disabled="role.readOnly">
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
    </uib-tabset>
  </div>
</ng-container>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
{/block}
