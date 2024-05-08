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

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks">
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('Widget')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
          <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
          {t}Save{/t}
        </button>
      </li>
    </ul>
  </div>
{/block}

{block name="rightColumn" append}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="WIDGET_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
      </div>
      {include file="ui/component/content-editor/accordion/textarea.tpl" title="{t}Description{/t}" field="description"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iField="title" iRequired=true iTitle="{t}Name{/t}" iValidation=true}
      <div class="controls">
        <div class="row">
          <div class="col-lg-6 col-md-6">
            <div class="form-group no-margin">
              <label class="form-label">
                {t}Type{/t}
              </label>
              <div>
                <div class="radio radio-inline p-l-4 ml-3">
                  <input id="renderlet-html" ng-change="resetContent()" ng-model="item.widget_type" ng-value="" type="radio" ng-checked="!item.widget_type">
                  <label for="renderlet-html">
                    HTML
                  </label>
                </div>
                <div class="radio radio-inline ml-3">
                  <input id="renderlet-intelligent" ng-change="resetContent()" ng-model="item.widget_type" ng-value="'intelligentwidget'" type="radio">
                  <label for="renderlet-intelligent">
                    {t}IntelligentWidget{/t}
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6" ng-if="item.widget_type">
            <div class="form-group no-margin">
              <label class="form-label">
                {t}Content{/t}
              </label>
              <div class="controls controls-validation">
                <ui-select class="block" name="content" theme="select2" ng-model="item.class" required>
                  <ui-select-match placeholder="{t}Select a type{/t}">
                    [% $select.selected.name %]
                  </ui-select-match>
                  <ui-select-choices position="up" repeat="item.id as item in data.extra.classes | filter: { name: $select.search }">
                    <div ng-bind-html="item.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
                {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iForm="form.content" iNgModel="item.class" iValidation=true}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!item.widget_type && !displayMultiBody()">
        {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Content{/t}" field="body" imagepicker=true rows=5}
      </div>
      <div class="ng-cloak clearfix m-t-15" ng-show="!item.widget_type && displayMultiBody()">
        <ul class="fake-tabs">
          <li ng-repeat="(slug_key, slug_value) in data.extra.locale.slugs" ng-class="{ 'active': language === slug_key }" ng-click="changeLanguage(slug_key)">
            [% data.extra.locale.available[slug_key] %]
          </li>
        </ul>
        <div class="form-group no-margin" ng-repeat="(slug_key, slug_value) in data.extra.locale.slugs" ng-show="language === slug_key">
          <label class="form-label clearfix m-t-10" for="body.[% slug_key %]">
            <div class="pull-left">{t}Content{/t}</div>
          </label>
          {acl isAllowed='PHOTO_ADMIN'}
            <div class="pull-right">
              <div class="btn btn-mini" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-dynamic-target="editor.body.[% slug_key %]" photo-editor-enabled="true">
                <i class="fa fa-plus"></i>
                {t}Insert image{/t}
              </div>
            </div>
          {/acl}
          <div class="controls">
            <textarea name="body.[% slug_key %]" id="body.[% slug_key %]" incomplete="incomplete" ng-model="item.body[slug_key]" onm-editor onm-editor-preset="simple" class="form-control" rows="80"></textarea>
          </div>
        </div>
      </div>
      <div class="ng-cloak" ng-show="item.widget_type && item.class">
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

{block name="modals"}
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
