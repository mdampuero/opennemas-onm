{extends file="common/extension/list.tpl"}

{block name="metaTitle"}
  > {t}Photos{/t}
{/block}

{block name="ngInit"}
  ng-controller="PhotoListCtrl" ng-init="init()"
{/block}

{block name="icon"}
  <i class="fa fa-picture-o m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/221735-opennemas-c%C3%B3mo-subir-im%C3%A1genes-para-mis-art%C3%ADculos" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  {t}Photos{/t}
{/block}

{block name="primaryActions"}
  {acl isAllowed="PHOTO_CREATE"}
    <li class="quicklinks">
      <a class="btn btn-primary" media-picker media-picker-mode="explore,upload" media-picker-mode-active="upload" media-picker-type="photo" id="upload-button">
        <span class="fa fa-cloud-upload"></span> {t}Upload{/t}
      </a>
    </li>
  {/acl}
{/block}

{block name="selectedActions"}
  {acl isAllowed="PHOTO_DELETE"}
    <li class="quicklinks">
      <span class="h-seperate"></span>
    </li>
    <li class="quicklinks">
      <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
        <i class="fa fa-trash-o fa-lg"></i>
      </button>
    </li>
  {/acl}
{/block}

{block name="leftFilters"}
  <li class="m-r-10 quicklinks">
    <div class="input-group input-group-animated">
      <span class="input-group-addon">
        <i class="fa fa-search fa-lg"></i>
      </span>
      <input class="input-min-45 input-300" ng-class="{ 'dirty': criteria.title }" name="name" ng-keyup="searchByKeypress($event)" ng-model="criteria.title" placeholder="{t}Search{/t}" type="text">
      <span class="input-group-addon input-group-addon-inside pointer ng-cloak no-animate" ng-click="clear('title')" ng-show="criteria.title">
        <i class="fa fa-times"></i>
      </span>
    </div>
  </li>
  <li class="hidden-xs ng-cloak quicklinks">
    <select name="month" ng-model="criteria.month">
      <option value="">{t}All months{/t}</option>
      <optgroup label="[% year.name %]" ng-repeat="year in data.extra.years">
        <option value="[% month.value %]" ng-repeat="month in year.months">
          [% month.name %] ([% year.name %])
        </option>
      </optgroup>
    </select>
  </li>
{/block}

{block name="list"}
  {include file="photo/list.table.tpl"}
  {include file="photo/list.grid.tpl"}
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>

  <script type="text/ng-template" id="modal-image">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
      <h4 class="modal-title">{t}Image preview{/t}</h4>
    </div>
    <div class="modal-body">
      <div class="resource">
        <span ng-if="template.selected.type_img == 'swf'">
          <swf-object swf-params="{ wmode: 'opaque' }" swf-url="{$smarty.const.INSTANCE_MEDIA}/images[% template.selected.path_file %][% template.selected.name %]" swf-width="570"></swf-object>
        </span>
        <span ng-if="template.selected.type_img !== 'swf'">
          <img class="img-responsive" ng-src="{$smarty.const.INSTANCE_MEDIA}/images{$MEDIA_IMG_URL}[% template.selected.path_file + template.selected.name %]"/>
        </span>
      </div>
    </div>
  </script>
{/block}
