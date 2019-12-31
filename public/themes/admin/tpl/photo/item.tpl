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

{block name="title"}
  <a class="no-padding" href="{url name=backend_photos_list}">
    {t}Photos{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      {include file="ui/component/content-editor/accordion/tags.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.when = !expanded.when">
        <i class="fa fa-cog m-r-10"></i>
        {t}Parameters{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.when }"></i>
      </div>
    </div>
    <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.when }">
      <div class="row">
        <div style="margin-top: 10px" class="photo-information">
          <div class="col-sm-6">
            <strong>{t}Size{/t}</strong> [% item.size %] KB
          </div>
          <div class="col-sm-6">
            <strong>{t}Resolution{/t}</strong> [% item.width %] X [% item.height %]
          </div>
        </div>
      </div>
  </div>
</div>
{/block}

{block name="leftColumn"}
 <div class="grid simple">
    <div class="grid-body">
          <div class="thumbnail-wrapper">
            <div class="form-group">
              <div class="thumbnail-placeholder">
                <div class="dynamic-image-placeholder ng-cloak" ng-if="item">
                  <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" ng-if="item" only-image="true">
                  </dynamic-image>
                </div>
              </div>
            </div>
          {include file="ui/component/input/text.tpl" iField="title" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
          {include file="ui/component/content-editor/textarea.tpl"  title="{t}Description{/t}" field="description" rows=20}
        </div>
    </div>
  </div>
{/block}
