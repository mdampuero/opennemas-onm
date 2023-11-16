{extends file="common/extension/list.grid.tpl"}

{block name="begin-wrapper"}
  <div class="content-wrapper">
{/block}

{block name="item"}
  <div class="pointer" ng-click="select(item)">
    <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" transform="zoomcrop,400,400">
      <div class="hidden-select" ng-click="select(item);toggleItem(item); xsOnly($event, toggle, item)"></div>
      <div class="thumbnail-actions thumbnail-actions-3x ng-cloak">
        {acl isAllowed="PHOTO_UPDATE"}
          <a class="thumbnail-action" href="[% routing.generate('backend_photo_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}">
            <i class="fa fa-pencil fa-2x text-default"></i>
          </a>
        {/acl}
        {acl isAllowed="PHOTO_DELETE"}
          <div class="thumbnail-action" ng-click="delete(item.pk_content)" uib-tooltip="{t}Delete{/t}" tooltip-class="tooltip-danger">
            <i class="fa fa-trash-o fa-2x text-danger"></i>
          </div>
        {/acl}
        <a class="thumbnail-action" ng-click="launchPhotoEditor(item)" uib-tooltip="{t}Enhance{/t}" tooltip-class="tooltip-info">
          <i class="fa fa-sliders fa-2x text-info"></i>
        </a>
      </div>
    </dynamic-image>
  </div>
{/block}

{block name="master-row"}
<div class="row master-row ng-cloak">
  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-0 infinite-col media-item"> </div>
</div>
{/block}

{block name="sidebar"}
  <div class="content-sidebar hidden-sm ng-cloak" ng-if="app.mode === 'grid'">
    <div class="center p-t-15" ng-if="!selected.lastSelected">
      <h4>{t}No item selected{/t}</h4>
      <h6>{t}Click in one item to show information about it{/t}</h6>
    </div>
    <h4 class="ng-cloak" ng-show="selected.lastSelected">{t}Image details{/t}</h4>
    <div ng-if="selected.lastSelected">
      <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)" ng-if="!isFlash(selected.lastSelected)">
        <dynamic-image autoscale="true" class="img-thumbnail no-animate" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="selected.lastSelected" only-image="true" transform="thumbnail,220,220"></dynamic-image>
      </div>
      <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected)" ng-if="isFlash(selected.lastSelected)">
        <dynamic-image autoscale="true" class="img-thumbnail no-animate" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="selected.lastSelected" only-image="true"></dynamic-image>
      </div>
      <ul class="media-information">
        <li>
          <a class="btn btn-block btn-default" ng-href="[% routing.generate('backend_photo_show', { id: selected.lastSelected.pk_content}) %]">
              <i class="fa fa-edit ng-isolate-scope"></i>
              {t}Edit{/t}
          </a>
        </li>
        <li>
          <a class="btn btn-block btn-info" ng-click="launchPhotoEditor(selected.lastSelected)">
              <i class="fa fa-sliders"></i>
              {t}Enhance{/t}
          </a>
        </li>
        <li>
          <a class="btn btn-block btn-white" href="{$app.instance->getBaseUrl()}{$smarty.const.INSTANCE_MEDIA}[% selected.lastSelected.path %]" target="_blank">
            <i class="fa fa-external-link m-r-5"></i>
            {t}Link{/t}
          </a>
        </li>
        <li>
          <label class="text-bold">
            {t}Name{/t}
          </label>
          <span class="m-l-10">
            [% selected.lastSelected.title %]
          </span>
        </li>
        <li>
          <label class="text-bold">
            {t}Created{/t}
          </label>
          <span class="m-l-10">
            [% selected.lastSelected.created %]
          </span>
        </li>
        <li>
          <div class="row">
            <div class="col-xs-6">
              <label class="text-bold">
                {t}Resolution{/t}
              </label>
              <span class="badge badge-default m-l-10 text-bold">
                [% selected.lastSelected.width %] x [% selected.lastSelected.height %]
              </span>
            </div>
            <div class="col-xs-6">
              <label class="text-bold">
                {t}Size{/t}
              </label>
              <span class="badge badge-default m-l-10 text-bold">
                [% selected.lastSelected.size %] KB
              </span>
            </div>
          </div>
        </li>
        <li>
          <label class="text-bold" for="description">
            {t}Description{/t}
            <div class="pull-right">
              <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': saving, 'fa-check text-success': saved, 'fa-times text-danger': error }"></i>
            </div>
          </label>
          <textarea id="description" ng-blur="saveDescription(selected.lastSelected.id)" ng-model="selected.lastSelected.description" cols="30" rows="10"></textarea>
        </li>
      </ul>
    </div>
  </div>
{/block}

{block name="end-wrapper"}
  </div>
{/block}
