{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Photos{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="PhotoCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}


{block name="icon"}
  <i class="fa fa-picture-o m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221735-opennemas-c%C3%B3mo-subir-im%C3%A1genes-para-mis-art%C3%ADculos" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10">
          <div class="thumbnail-wrapper">
            <div class="form-group">
              <div class="thumbnail-placeholder">
                <div class="dynamic-image-placeholder ng-cloak" ng-if="item">
                  <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" ng-if="item" only-image="true">
                  </dynamic-image>
                </div>
              </div>
            </div>
          {include file="ui/component/content-editor/textarea.tpl"  title="{t}Description{/t}" field="description" rows=20}
        </div>
        <div class="col-md-1">
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" title="{t}Title{/t}" field="title"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" title="{t}Size{/t}" field="size"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" title="{t}Width{/t}" field="width"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" title="{t}Height{/t}" field="height"}
    </div>
  </div>
{/block}
