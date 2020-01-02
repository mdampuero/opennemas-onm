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
      <div class="grid-collapse-title ng-cloak">
        <i class="fa fa-cog m-r-10"></i>
        {t}Parameters{/t}
      </div>
    </div>
    <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.information = !expanded.information">
      <i class="fa fa-info-circle m-r-10"></i>{t}Information{/t}
      <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.information }"></i>
    </div>
    <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.information }">
      <div class="row">
        <div class="col-sm-6">
          <strong>{t}Resolution{/t}</strong> [% item.width %]px X [% item.height %]px
        </div>
        <div class="col-sm-6">
          <strong>{t}Size{/t}</strong> [% item.size %] KB
        </div>
      </div>
      <div class="form-group">
        <label style="margin-top:10px" for="author_name" class="form-label">{t}Copyright{/t}</label>
        <div class="controls">
          <div class="input-group">
            <input class="form-control" type="text" id="author_name" name="author_name" ng-model="item.author_name"/>
            <span class="input-group-addon add-on">
              <span class="fa fa-copyright"></span>
            </span>
        </div>
      </div>
      <div class="iptc-exif">
        <h5 class="toggler toggler" ng-click="info = !info">
        {t escape=off}View advanced data{/t}
        <span>
          <i class="fa" ng-class="{ 'fa-plus-square-o': !info, 'fa-minus-square-o': info }"></i>
        </span>
        </h5>
      </div>
      <div class="info ng-cloak" ng-if="info">
        {if is_null($photo->exif) neq true}
          <h6>{t}EXIF Data{/t}</h6>
          <div id="exif" class="photo-static-info">
            {foreach $photo->exif as $name => $value}
              {foreach $value as $d => $dato}
              <p>
                <strong>{$d}</strong> : {$dato}
              </p>
              {/foreach}
            {/foreach}
          </div>
        {else}
          <div id="exif" class="photo-static-info">
            {t}No available EXIF data.{/t}
          </div>
        {/if}

        {if isset($photo->myiptc)}
          <h6>{t}IPTC Data{/t}</h6>
          <div id="iptc" class="photo-static-info">
            {foreach $photo->myiptc as $name => $value}
            {if $name}
            <p>
              <strong>{$name}</strong> : {$value}
            </p>
            {/if}
            {/foreach}
            <br />
          </div>
        {else}
          <div id="iptc" class="photo-static-info">
            {t}No available IPTC data.{/t}
          </div>
        {/if}
      </div><!-- /additional-info -->
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
