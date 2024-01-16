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

{block name="primaryActions"}
  <div class="all-actions pull-right">
    <ul class="nav quick-section">
      <li class="quicklinks hidden-xs ng-cloak" ng-if="draftSaved">
        <h5>
          <i class="p-r-15">
            <i class="fa fa-check"></i>
            {t}Draft saved at {/t}[% draftSaved %]
          </i>
        </h5>
      </li>
      <li class="quicklinks">
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('Files')}Config form: '%1'{/t}">
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
      </div>
      {include file="ui/component/content-editor/accordion/category.tpl" field="categories[0]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-lg-4">
          <div class="row">
            <div class="col-lg-12 col-lg-offset-0 col-sm-6 col-sm-offset-3">
              <div class="p-b-30 p-l-30 p-r-30 p-t-35">
                <div class="text-center">
                  <i class="fa fa-file-o fa-3x" ng-if="item.path"></i>
                  <i class="fa fa-warning fa-3x text-warning" ng-if="!item.path"></i>
                  <p class="m-t-15 text-center">
                    <strong ng-if="item.path" title="[% getFileName() %]">
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
                <a class="btn btn-white btn-block m-t-15" ng-show="item.path && !item.path.name" ng-href="{$app.instance->getBaseUrl()}[% data.extra.paths.attachment +  item.path %]" target="_blank">
                  <i class="fa fa-download m-r-5"></i>
                  {t}Download{/t}
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          {include file="ui/component/input/text.tpl" iField="title" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
          {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Description{/t}" field="description" rows=5}
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-draft">
    {include file="common/modals/_draft.tpl"}
  </script>
  <script type="text/ng-template" id="modal-translate">
    {include file="common/modals/_translate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
