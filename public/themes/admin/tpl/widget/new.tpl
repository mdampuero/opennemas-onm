{extends file="base/admin.tpl"}

{block name="content" append}
  <form action="{if isset($widget)}{url name=admin_widget_update id=$widget->id}{else}{url name=admin_widget_create}{/if}" method="post" ng-controller="WidgetCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-puzzle-piece"></i>
                {t}Widgets{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>
                {if !isset($widget->id)}
                  {t}Creating widget{/t}
                {else}
                  {t}Editing widget{/t}
                {/if}
              </h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_widgets}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      {render_messages}
      <div class="row">
      <div class="col-sm-8">
        <div class="grid simple">
        <div class="grid-body">
          <div class="form-group">
            <label for="metadata" class="form-label">{t}Widget name{/t}</label>
            <div class="controls">
              <input type="text" id="title" name="title" value="{$widget->title|default:""}" required="required" class="form-control"/>
            </div>
          </div>
          <div class="form-group">
            <label for="renderlet" class="form-label">{t}Widget type{/t}</label>
            <div class="controls">
              <select name="renderlet" id="renderlet" ng-model="renderlet">
                <option value="intelligentwidget" {if isset($widget) && $widget->renderlet == 'intelligentwidget'}selected="selected"{/if}>{t}Intelligent Widget{/t}</option>
                <option value="html" {if isset($widget) && $widget->renderlet == 'html'}selected="selected"{/if}>{t}HTML{/t}</option>
                <option value="smarty" {if isset($widget) && $widget->renderlet == 'smarty'}selected="selected"{/if}>{t}Smarty{/t}</option>
              </select>
            </div>
          </div>
          <div class="form-group ng-cloak">
            <label for="description" class="form-label">{t}Description{/t}</label>
            <div class="controls">
              <textarea name="description" id="description" class="form-control" rows="4" ng-model="description">{$widget->description|default:""}</textarea>
            </div>
          </div>
          <div class="form-group ng-cloak">
            <label for="description" class="form-label">
              {t}Content{/t}
            </label>
            <div class="pull-right">
              {acl isAllowed='PHOTO_ADMIN'}
              <div class="btn btn-default btn-mini ng-cloak form-control" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="5" media-picker-target="editor.content" ng-show="renderlet == 'html'">{t}Insert image{/t}</div>
              {/acl}
            </div>
            <div class="controls">
              <div ng-show="renderlet !== 'intelligentwidget'">
                <textarea onm-editor onm-editor-preset="simple" name="content" class="form-control">{$widget->content|default:""}</textarea>
              </div>
              <div ng-show="renderlet == 'intelligentwidget'">
                <select name="intelligent_type" id="intelligent_type" ng-model="intelligent_type">
                  {foreach from=$all_widgets item=w}
                  <option value="{$w}" {if isset($widget) && trim($widget->content) == $w}selected="selected"{/if}>{$w}</option>
                  {/foreach}
                </select>
              </div>
            </div>
          </div>
          <div  class="form-group ng-cloak" ng-show="renderlet == 'intelligentwidget'">
          <label for="params" ng-if="params.length > 0">{t}Parameters{/t}</label>
          <div id="params">
            <input type="hidden" name="parsedParams" ng-model="parsedParams" ng-value="parsedParams" ng-init="parseParams({json_encode($widget->params)|clear_json})">
            <label for="" class="sr-only">{t}Parameter name{/t}</label>
            <div class="form-group ng-cloak" ng-repeat="param in params track by $index">
              <input type="text" name="items[]" ng-model="param.name" placeholder="{t}Parameter name{/t}" />
              <input type="text" name="values[]" ng-model="param.value"  placeholder="{t}Parameter value{/t}">
              <button type="button" ng-click="removeParameter($index)" class="btn addon del"><i class="fa fa-trash"></i></button>
            </div>
          </div>
          <br>
          <a id="add_param" class="btn" ng-click="addParameter()">
            <i class="fa fa-plus"></i>
            {t}Add parameter{/t}
          </a>
          </div>
        </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="grid simple">
        <div class="grid-title">
          {t}Parameters{/t}
        </div>
        <div class="grid-body">
          <div class="form-group">
            <div class="checkbox">
              <input id="content_status" name="content_status" {if (isset($widget) && $widget->content_status eq 1)}checked{/if}  value="1" type="checkbox"/>
              <label for="content_status">
              {t}Published{/t}
              </label>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </form>
{/block}
