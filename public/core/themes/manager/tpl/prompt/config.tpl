<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate(routes.list) %]">
              <i class="fa fa-file-o"></i>
              {t}Prompts{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            {t}Configs{/t}
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate(routes.list) %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks m-r-10">
            <a class="btn btn-white" ng-click="openImportModal()">
              <span class="fa fa-sign-in"></span>
              {t}Import{/t}
            </a>
          </li>
          <li class="quicklinks m-r-10">
            <a class="btn btn-white" ng-href="{url name=manager_ws_prompt_config_download}?token=[% security.token %]">
              <span class="fa fa-download"></span>
              {t}Download{/t}
            </a>
          </li>
          <li class="quicklinks">
            <button class="btn btn-loading btn-success text-uppercase" ng-click="!item.id ? save() : update()" ng-disabled="promptForm.$invalid || saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <div class="grid simple onm-shadow">
    <div class="grid-body ng-cloak">
      <h4>{t}Roles{/t}</h4>
      <div class="form-group">
        <div class="controls">
          <div class="row" ng-repeat="role in settings.openai_roles track by $index">
            <div class="col-lg-4 col-md-3">
              <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required>
            </div>
            <div class="col-lg-7 col-md-7">
              <input class="form-control" ng-model="role.prompt" placeholder="{t}Prompt{/t}" type="text" required>
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
  </div>
  <div class="grid simple onm-shadow">
    <div class="grid-body ng-cloak">
      <h4>{t}Tones{/t}</h4>
      <div class="form-group">
        <div class="controls">
          <div class="row" ng-repeat="role in settings.openai_tones track by $index">
            <div class="col-lg-4 col-md-3">
              <input class="form-control" ng-model="role.name" placeholder="{t}Name{/t}" type="text" required>
            </div>
            <div class="col-lg-7 col-md-7">
              <input class="form-control" ng-model="role.description" placeholder="{t}Description{/t}" type="text" required>
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
  </div>
  <div class="grid simple onm-shadow">
    <div class="grid-body ng-cloak">
      <h4>{t}Instructions{/t}</h4>
      <div class="form-group">
        <div class="controls">
          <div class="row" ng-repeat="role in settings.openai_instructions track by $index">
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
              <input class="form-control" ng-model="role.value" placeholder="{t}Instruction{/t}" type="text" required>
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
  </div>
</div>
<script type="text/ng-template" id="instance">
  <span ng-bind-html="$highlight($getDisplayText())"></span>
  </div>
</script>
<script type="text/ng-template" id="modal-import-settings">
  {include file="common/modalImportSettings.tpl"}
</script>
