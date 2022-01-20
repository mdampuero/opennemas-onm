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
        <a class="btn btn-link" class="" ng-click="expansibleSettings()" title="{t 1=_('Photo')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="submit($event)" type="button">
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
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="thumbnail-wrapper">
        <div class="dynamic-image-placeholder ng-cloak">
          <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" only-image="false"></dynamic-image>
        </div>
      </div>
      <div class="m-b-30 m-t-15 text-center">
        <span class="m-r-30">
          <strong class="m-r-10">
            {t}Size{/t}
          </strong>
          <span class="badge badge-default text-bold">
            [% item.size %] KB
          </span>
        </span>
        <span class="m-r-30">
          <strong class="m-r-10">
            {t}Resolution{/t}
          </strong>
          <span class="badge badge-default text-bold">
            [% item.width %] x [% item.height %]
          </span>
        </span>
        <span>
          <strong class="m-r-10">
            {t}Original{/t}
          </strong>
          <span>
            <a class="badge badge-default text-bold" href="{$smarty.const.INSTANCE_MEDIA}[% extra.paths.photo + item.path %]" target="_blank">
              <i class="fa fa-external-link m-r-5"></i>
              {t}Link{/t}
            </a>
          </span>
        </span>
      </div>
      <div class="form-group">
        {include file="ui/component/input/text.tpl" iField="title" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
        {include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=20}
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-draft">
    {include file="common/modals/_draft.tpl"}
  </script>
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}
