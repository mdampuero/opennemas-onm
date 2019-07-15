{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Polls{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="PollCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-pie-chart m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_polls_list}">
    {t}Polls{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="POLL_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
        <div class="m-t-5">
          {acl isAllowed="POLL_FAVORITE"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Favorite{/t}" field="favorite"}
          {/acl}
        </div>
        <div class="m-t-5">
          {acl isAllowed="POLL_HOME"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Home{/t}" field="in_home"}
          {/acl}
        </div>
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/category.tpl" field="item.category"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" route="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i>
        {t}Parameters{/t}
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.closed = !expanded.closed">
        <i class="fa fa-calendar m-r-10"></i>
        {t}Vote end date{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.closed }"></i>
        <span class="badge badge-default m-r-10 m-t-2 pull-right text-uppercase"  ng-show="!expanded.closed && !isClosed(item)">
          <strong>
            {t}Open{/t}
          </strong>
        </span>
        <span class="badge badge-default m-r-10 m-t-2 pull-right text-uppercase" ng-show="!expanded.closed && isClosed(item)">
          <strong>
            {t}Closed{/t}
          </strong>
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.closed }">
        <div class="form-group no-margin">
          <div class="controls">
            <div class="input-group">
              <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current="true" datetime-picker-min="item.created" id="closetime" name="closetime" ng-model="item.params.closetime" type="datetime">
              <span class="input-group-addon add-on">
                <span class="fa fa-calendar"></span>
              </span>
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
      {include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true counter=true}
      {include file="ui/component/content-editor/input-text.tpl" title="{t}Pretitle{/t}" field="pretitle" counter=true}
      {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>
        <i class="fa fa-comment m-r-5"></i>
        {t}Answers{/t}
      </h4>
    </div>
    <div class="grid-body">
      <div class="form-group">
        <div class="controls">
          <div id="answers">
            <div class="form-group" ng-repeat="answer in item.items track by $index">
              <div class="input-group">
                <input class="form-control" ng-model="answer.item" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.items[$index].item[data.extra.locale.default] : '' %]" uib-tooltip="{t}Original{/t}: [% data.item.items[$index].item[data.extra.locale.default] %]" tooltip-enable="config.locale.multilanguage && config.locale.default !== config.locale.selected"required type="text"/>
                <div class="input-group-addon">
                  <small ng-if="answer.votes > 0">{t}Votes{/t}:  [% answer.votes %] / [% item.total_votes %]</small>
                  <small ng-if="answer.votes <= 0">{t}No votes{/t}</small>
                </div>
                <div class="input-group-btn">
                  <button class="btn btn-danger" ng-click="removeAnswer($index)" type="button">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3 p-b-15 p-t-15">
          <button class="btn btn-block btn-loading btn-success" ng-click="addAnswer()">
            <h4 class="text-uppercase text-white">
              <i class="fa fa-plus m-r-5"></i>
              {t}Add{/t}
            <h4>
          </button>
        </div>
      </div>
    </div>
  </div>
{/block}
