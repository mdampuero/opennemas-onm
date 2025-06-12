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
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!item.id">{t}New prompt{/t}</span>
            <span ng-if="item.id">{t}Prompt edit{/t}</span>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_onmai_prompt_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-loading btn-success text-uppercase" ng-click="!item.id ? save() : update()" >
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="extra">
  <form name="promptForm" novalidate>
    <div class="row">
      <div class="col-md-8">
        <div class="grid simple">
          <div class="grid-body adstxt-form">
            <div class="row">
              <div class="form-group col-md-5">
                <label class="form-label" for="name">{t}Title{/t}</label>
                <div class="input-with-icon right">
                  <input class="form-control" id="name" name="name" ng-model="item.name" required type="text" maxlength="64"/>
                </div>
              </div>
              <div class="form-group col-md-3">
                <label class="form-label" for="name">{t}Model{/t}</label>
                <div class="">
                  <select class="form-control form-control-lg" ng-model="item.model">
                    <option value="">{t}Default model{/t}</option>
                    <option value="[% item.id %]" ng-repeat="item in extra.onmai_models">[% item.title %]</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-4 form-group">
                <label for="template" >{t}Target{/t}</label>
                <div >
                  <tags-input add-from-autocomplete-only="true" ng-model="item.instances" display-property="name">
                    <auto-complete debounce-delay="500" source="autocomplete($query)" min-length="0" load-on-focus="true" load-on-empty="true" template="instance"></auto-complete>
                  </tags-input>
                </div>
              </div>
              <div class="col-md-2 col-sm-6 form-group">
                <label>{t}Mode{/t}</label>
                <ui-select name="mode" class="form-control" ng-change="generatePreview()" theme="select2" ng-model="item.mode" search-enabled="false" required ng-init="options = [ { name: '{t}New{/t}', key: 'New'}, { name: '{t}Edit{/t}', key: 'Edit'}, { name: '{t}Agency{/t}', key: 'Agency'}]">
                  <ui-select-match>
                    [% $select.selected.name %]
                  </ui-select-match>
                  <ui-select-choices repeat="item.key as item in options | filter: $select.search">
                    <div ng-bind-html="item.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
              </div>
              <div class="col-md-3 col-sm-6 form-group" ng-if="item.mode != 'Agency'">
                <label>{t}Field{/t}</label>
                <ui-select name="mode" class="form-control" ng-change="" theme="select2" ng-model="item.field" search-enabled="false" required ng-init="options = [ { name: '{t}Titles{/t}', key: 'titles'}, { name: '{t}Descriptions{/t}', key: 'descriptions'}, { name: '{t}Bodies{/t}', key: 'bodies' } ]">
                  <ui-select-match>
                    [% $select.selected.name %]
                  </ui-select-match>
                  <ui-select-choices repeat="item.key as item in options | filter: $select.search">
                    <div ng-bind-html="item.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
              </div>
              <div class="col-md-3 col-sm-6 form-group">
                <label>{t}Default tone{/t}</label>
                <ui-select name="tone" class="form-control" theme="select2" ng-change="generatePreview()" ng-model="item.tone">
                  <ui-select-match>
                    [% $select.selected.name %]
                  </ui-select-match>
                  <ui-select-choices repeat="item.name as item in extra.onmai_tones | filter: { name: $select.search }" position='down'>
                    <div ng-bind-html="item.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
              </div>
              <div class="col-md-4 col-sm-6 form-group">
                <label>{t}Default role{/t}</label>
                  <ui-select name="role" class="form-control" ng-change="generatePreview()" theme="select2" ng-model="item.role">
                    <ui-select-match>
                      [% $select.selected.name %]
                    </ui-select-match>
                    <ui-select-choices repeat="item.name as item in extra.onmai_roles | filter: { name: $select.search, field: filterRole }" position='down'>
                      <div ng-bind-html="item.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
              </div>
              <div class="form-group col-md-12">
                <label class="form-label" for="name">{t}Objective{/t}</label>
                <textarea name="prompt" id="prompt" ng-change="delayedPreview()" ng-model="item.prompt" class="form-control" rows="8" required maxlength="2048"></textarea>
              </div>
              <div class="col-md-12">
                <label class="form-label" for="name">{t}Final prompt preview{/t}</label>
                <textarea name="preview" id="preview" readonly ng-model="preview" class="form-control" rows="18" required maxlength="2048"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="grid simple">
          <div class="grid-body adstxt-form">
            <div class="form-group">
              <div class="checkbox">
                <input id="content-status" ng-false-value="0" ng-model="item.status" ng-true-value="1" type="checkbox">
                <label for="content-status">{t}Published{/t}</label>
              </div>
            </div>
            <div class="form-group">
              <hr>
            </div>
            <div class="form-group">
              <label class="form-label"><b>{t}Instructions{/t}</b></label>
              <div class="input-with-icon right">
                <input class="form-control"
                      id="search"
                      name="search"
                      ng-model="searchText"
                      type="text"
                      maxlength="64"
                      placeholder="{t}Search{/t}..." />
              </div>
            </div>
            <span class="badge text-capitalize badge-success pointer"
                  style="margin: 5px 5px; padding: 5px 8px;"
                  uib-tooltip="[% item.value %]" tooltip-placement="top"
                  ng-repeat="id in instructionsAvailables | filter:filterInstructions"
                  ng-click="selectInstruction(id)">
              [% getInstructionTitle(id) %]
            </span>
            <div class="form-group">
              <hr>
            </div>
            <label class="form-label"><b>{t}Selected instructions{/t}</b></label>
            <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel m-t-5 p-t-15 bg-light">
              <div class="form-status text-left">
                <span class="badge text-capitalize badge-primary pointer m-a-5"
                      ng-if="instructionExists(id)"
                      uib-tooltip="{t}Remove{/t}" tooltip-placement="top"
                      ng-repeat="id in item.instructions"
                      ng-click="deselectInstruction(id)"
                      style="margin: 5px 5px; padding: 5px 8px;">
                  [% getInstructionTitle(id) %]
                  <a aria-label="Close"style="margin-left: 5px;">
                    <span aria-hidden="true">&times;</span>
                  </a>
                </span>
              </div>
              <p ng-if="!item.instructions.length" >{t}You have not selected any instructions{/t}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script type="text/ng-template" id="instance">
  <span ng-bind-html="$highlight($getDisplayText())"></span>
  </div>
</script>
