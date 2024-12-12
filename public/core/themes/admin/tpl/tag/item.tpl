{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Tags{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="TagCtrl" ng-init="getItem({$id}); flags.block.slug = true"
{/block}

{block name="icon"}
  <i class="fa fa-tags m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="[% routing.generate('backend_tags_list') %]">
    {t}Tags{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
        <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.visibility }" ng-click="expanded.visibility = !expanded.visibility">
          <i class="fa fa-eye m-r-10"></i>{t}Visibility{/t}
          <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.visibility }"></i>
          <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.visibility">
            <span ng-show="item.private">{t}Private{/t}</span>
            <span ng-show="!item.private">{t}Public{/t}</span>
          </span>
        </div>
        <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.visibility }">
          <div class="form-group no-margin">
            <div class="checkbox">
              <input class="form-control" id="private" name="private" ng-false-value="0" ng-model="item.private" ng-true-value="1" type="checkbox">
              <label for="private" class="form-label">
                {t}Private{/t}
              </label>
            </div>
            <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
              <i class="fa fa-info-circle m-r-5 text-info"></i>
              {t}Check this option to prevent the tag from appearing in your content. Additionally, the page for this tag will not be available.{/t}
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-md-6">
          {include file="ui/component/input/text.tpl" iField="name" iMessageField="name" iFlag="validating" iNgActions="ng-blur=\"generate()\" ng-change=\"isValid()\"" iRequired=true iTitle="{t}Name{/t}" iValidation=true}
        </div>
        <div class="col-md-6">
          {include file="ui/component/input/slug.tpl" iField="slug" iFlag="slug" iNgModel="item.slug" iRequired=true iTitle="{t}Slug{/t}" iValidation=true}
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          {include file="ui/component/input/text.tpl" iField="seo_title" iMessageField="seo_title" iRequired=false iTitle="{t}Seo title{/t}" iValidation=false}
        </div>
        <div class="col-sm-6">
          {include file="ui/component/input/text.tpl" iField="header_1" iMessageField="seo_title" iRequired=false iTitle="{t}H1 Header{/t}" iHelp="{t}If this field is left empty, the default Title will be displayed.{/t}" iValidation=false}
        </div>
        <div class="col-sm-6 form-group" ng-if="config.locale.multilanguage">
          <label class="form-label" for="locale">
            {t}Language{/t}
          </label>
          <div class="controls">
            <select class="form-control" name="locale" ng-model="item.locale">
              <option value="">{t}Any{/t}</option>
              <option value="[% id %]" ng-repeat="(id, name) in config.locale.available">[% name %]</option>
            </select>
          </div>
        </div>
      </div>
      <div class="form-group no-margin">
        <label class="form-label" for="description">
          {t}Seo description{/t}
        </label>
        <div class="controls">
          <textarea name="description" id="description" ng-model="item.description" class="form-control" rows="5"></textarea>
        </div>
      </div>
    </div>
  </div>
{/block}
