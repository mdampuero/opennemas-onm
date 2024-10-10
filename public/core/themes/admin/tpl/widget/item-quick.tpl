<div class="row">
  <div class="col-xs-12 col-sm-7 col-md-8">
    {block name="leftColumn"}
      <div class="grid simple">
        <div class="grid-body">
          {include file="ui/component/input/text.tpl" iField="title" iRequired=true iTitle="{t}Name{/t}" iValidation=true}
          <div class="ng-cloak" ng-show="!item.widget_type && !displayMultiBody()">
            {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Content{/t}" minheight="500" field="body" imagepicker=true rows=5}
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
                <textarea name="body.[% slug_key %]" id="body.[% slug_key %]" incomplete="incomplete" ng-model="item.body[slug_key]" onm-editor onm-editor-preset="simple" onm-editor-height="500" class="form-control" rows=50></textarea>
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
  </div>
  <div class="col-xs-12 col-sm-5 col-md-4" style="position:sticky;top: 65px;">
    {block name="rightColumn" append}
      <div class="grid simple">
        <div class="grid-body no-padding">
          <div class="grid-collapse-title" style="border-top: 0;">
            {acl isAllowed="WIDGET_AVAILABLE"}
              {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
            {/acl}
          </div>
          {include file="ui/component/content-editor/accordion/textarea.tpl" title="{t}Description{/t}" field="description"}
          {include file="ui/component/content-editor/accordion/scheduling.tpl"}
        </div>
      </div>
    {/block}
  </div>
</div>
