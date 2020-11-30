{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Albums{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="AlbumCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-stack-overflow m-r-10"></i>
  <a class="help-icon hidden-xs" href="http://help.opennemas.com/knowledgebase/articles/745938-opennemas-c%C3%B3mo-crear-%C3%A1lbumes-galer%C3%ADas-de-imagene" target="_blank" uib-tooltip="{t}Help{/t}" tooltip-placement="bottom">
    <i class="fa fa-question"></i>
  </a>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_albums_list}">
    {t}Albums{/t}
  </a>
{/block}

{block name="primaryActions"}
  <li class="quicklinks">
    <button class="btn btn-loading btn-success text-uppercase" ng-click="submit($event)" type="button">
      <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
      {t}Save{/t}
    </button>
  </li>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="ALBUM_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
        <div class="m-t-5">
          {acl isAllowed="ALBUM_FAVORITE"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Favorite{/t}" field="favorite"}
          {/acl}
        </div>
        <div class="m-t-5">
          {acl isAllowed="ALBUM_HOME"}
            {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Home{/t}" field="in_home"}
          {/acl}
        </div>
        <div class="m-t-5">
          {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/author.tpl"}
      {include file="ui/component/content-editor/accordion/category.tpl" field="category_id"}
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i>
        {t}Parameters{/t}
      </div>
      {include file="ui/component/content-editor/accordion/image.tpl" title="{t}Cover image{/t}" field="cover"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="agency" title="{t}Agency{/t}"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      {include file="ui/component/content-editor/textarea.tpl" class="no-margin" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>
        <i class="fa fa-picture-o m-r-5"></i>
        {t}Images{/t}
      </h4>
      <button class="btn btn-link no-padding m-t-2 pull-right" ng-click="setMode(app.mode === 'grid' ? 'list' : 'grid')" type="button">
        <i class="fa" ng-class="{ 'fa-th': app.mode === 'grid', 'fa-list': app.mode === 'list' }"></i>
      </button>
    </div>
    <div class="grid-body">
      <div class="row">
        <div class="col-md-12">
          <div ui-tree>
            <div class="album-photos album-photos-[% app.mode %]" ng-model="item.photos" ui-tree-nodes="">
              <div class="album-photos-item" ng-repeat="photo in item.photos" ui-tree-node>
                <span ui-tree-handle>
                  <span class="angular-ui-tree-icon"></span>
                </span>
                <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay['photo_'+ photo.pk_photo] }"></div>
                <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay['photo_'+ photo.pk_photo] }">
                  <p>{t}Are you sure?{/t}</p>
                  <div class="confirm-actions">
                    <button class="btn btn-link" ng-click="toggleOverlay('photo_'+ photo.pk_photo)" type="button">
                      <i class="fa fa-times fa-lg"></i>
                      {t}No{/t}
                    </button>
                    <button class="btn btn-link" ng-click="removeItem('item.photos', $index); removeItem('data.item.photos', $index);toggleOverlay('photo_'+ photo.pk_photo)" type="button">
                      <i class="fa fa-check fa-lg"></i>
                      {t}Yes{/t}
                    </button>
                  </div>
                </div>
                <div class="thumbnail-wrapper row">
                  <div ng-class="{ 'col-lg-2 col-sm-3': app.mode === 'list', 'col-xs-12': app.mode === 'grid' }">
                    <div class="dynamic-image-placeholder">
                      <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[photo.pk_photo]" transform="zoomcrop,200,200">
                        <div class="thumbnail-actions">
                          <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo_'+ photo.pk_photo)">
                            <i class="fa fa-trash-o fa-2x"></i>
                          </div>
                        </div>
                      </dynamic-image>
                    </div>
                  </div>
                  <div ng-class="{ 'col-lg-10 col-sm-9': app.mode === 'list', 'col-xs-12': app.mode === 'grid' }">
                    <div class="form-group no-margin">
                      <textarea class="album-thumbnail-description form-control" ng-model="photo.description" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.photos[index].description[data.extra.locale.default] : '' %]"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6 p-b-15 p-t-15" ng-class="{ 'col-lg-4 col-lg-offset-4 col-xs-offset-3': !item.photos || item.photos.length === 0, 'col-lg-3 col-lg-offset-3': item.photos && item.photos.length > 0 }">
          <button class="btn btn-block btn-default btn-loading" media-picker media-picker-ignore="item.photos" media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="150" media-picker-target="photos" type="button">
            <h5 class="text-uppercase">
              <i class="fa fa-plus m-r-5"></i>
              {t}Add{/t}
            </h5>
          </button>
        </div>
        <div class="col-lg-3 col-xs-6 p-b-15 p-t-15" ng-if="item.photos && item.photos.length > 0">
          <button class="btn btn-block btn-loading btn-danger" ng-click="empty()" type="button">
            <h5 class="text-uppercase text-white">
              <i class="fa fa-trash m-r-5"></i>
              {t}Empty{/t}
            </h5>
          </button>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-edit-album-error">
    {include file="album/modal.validate.tpl"}
  </script>
{/block}
