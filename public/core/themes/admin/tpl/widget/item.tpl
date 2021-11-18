{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Widgets{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="WidgetCtrl" ng-init="getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-puzzle-piece m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_widgets_list}">
    {t}Widgets{/t}
  </a>
{/block}

{block name="rightColumn" append}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="WIDGET_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
      </div>
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iField="title" iRequired=true iTitle="{t}Name{/t}" iValidation=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=4}
      <div class="controls">
        <div class="row">
          <div class="col-lg-6 col-md-6">
            <div class="form-group">
              <label class="form-label">
                {t}Type{/t}
              </label>
              <div class="radio m-b-5 m-t-5">
                <input id="renderlet-html" ng-change="resetContent()" ng-model="item.renderlet" ng-value="'html'" type="radio">
                <label for="renderlet-html">
                  HTML
                </label>
              </div>
              <div class="radio m-t-5">
                <input id="renderlet-intelligent" ng-change="resetContent()" ng-model="item.renderlet" ng-value="'intelligentwidget'" type="radio">
                <label for="renderlet-intelligent">
                  {t}IntelligentWidget{/t}
                </label>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6" ng-if="item.renderlet !== 'html'">
            <div class="form-group no-margin">
              <label class="form-label">
                {t}Content{/t}
              </label>
              <div class="controls controls-validation">
                <ui-select class="block" name="content" theme="select2" ng-model="item.content" required>
                  <ui-select-match placeholder="{t}Select a type{/t}">
                    [% $select.selected.name %]
                  </ui-select-match>
                  <ui-select-choices position="up" repeat="item.id as item in data.extra.types | filter: { name: $select.search }">
                    <div ng-bind-html="item.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
                {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iForm="form.content" iNgModel="item.content" iValidation=true}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="ng-cloak" ng-show="item.renderlet === 'html'">
        {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Content{/t}" field="content" imagepicker=true rows=5}
      </div>
      <div class="ng-cloak" ng-show="item.renderlet !== 'html' && item.content">
        <div class="p-b-50 p-t-50 spinner-wrapper" ng-if="flags.http.formLoading">
          <div class="loading-spinner"></div>
          <div class="spinner-text">{t}Loading{/t}...</div>
        </div>
        <div ng-show="!flags.http.formLoading && !widgetForm">
          <label for="params">{t}Parameters{/t}</label>
          <div class="form-group ng-cloak" ng-repeat="param in item.params track by $index">
            <div class="row">
              <div class="control col-md-2 col-sm-3 col-xs-12">
                <input class="form-control" ng-model="param.name" placeholder="{t}Parameter name{/t}" type="text">
              </div>
              <div class="control col-md-9 col-sm-7 col-xs-12">
                <input class="form-control" ng-model="param.value" placeholder="{t}Parameter value{/t}" type="text">
              </div>
              <div class="control col-md-1 col-sm-2 col-xs-12">
                <button class="btn btn-block btn-danger" ng-click="removeParameter($index)" type="button">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="col-xs-6 p-b-15 p-t-15 col-lg-4 col-lg-offset-4">
            <button class="btn btn-block btn-default btn-loading" ng-click="addParameter()" type="button">
              <h5 class="text-uppercase">
                <i class="fa fa-plus"></i>
                {t}Add{/t}
              </h5>
            </button>
          </div>
        </div>
        <div class="widget-form" ng-show="!flags.http.formLoading && widgetForm"></div>
      </div>
    </div>
  </div>
{/block}

