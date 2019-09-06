{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Files{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="AttachmentCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-paperclip m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_attachments_list}">
    {t}Files{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="ATTACHMENT_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
        <div class="m-t-5">
          {acl isAllowed="ATTACHMENT_FAVORITE"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Favorite{/t}" field="favorite"}
          {/acl}
        </div>
        <div class="m-t-5">
          {acl isAllowed="ATTACHMENT_HOME"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Home{/t}" field="in_home"}
          {/acl}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/category.tpl" field="item.category"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-lg-4 m-b-30">
          <div class="p-l-30 p-r-30 p-t-15">
            <div class="text-center">
              <div>
                  <i class="fa fa-file-o fa-3x" ng-if="item.path"></i>
                  <i class="fa fa-warning fa-3x text-warning" ng-if="!item.path"></i>
                </span>
              </div>
              <p class="m-t-15 text-center">
                <strong ng-if="item.path">
                  [% getFileName() %]
                </strong>
                <strong ng-if="!item.path">
                  {t}No file selected{/t}
                </strong>
              </p>
            </div>
            <label class="btn btn-default btn-block m-t-15" for="file">
              <input class="hidden" id="file" name="file" file-model="item.path" type="file"/>
              <span ng-if="!item.path">
                <i class="fa fa-plus m-r-5"></i>
                {t}Add{/t}
              </span>
              <span ng-if="item.path">
                <i class="fa fa-edit m-r-5"></i>
                {t}Change{/t}
              </span>
            </label>
            <a class="btn btn-white btn-block m-t-15" ng-show="item.path && !item.path.name" ng-href="[% data.extra.paths.attachment +  item.path %]" target="_blank">
              <i class="fa fa-download m-r-5"></i>
              {t}Download{/t}
            </a>
          </div>
        </div>
        <div class="col-lg-8">
          {include file="ui/component/content-editor/input-text.tpl" iCounter=true iField="title" iRequired=true iTitle="{t}Title{/t}"}
          {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Description{/t}" field="description" rows=5}
        </div>
      </div>
    </div>
  </div>
{/block}
