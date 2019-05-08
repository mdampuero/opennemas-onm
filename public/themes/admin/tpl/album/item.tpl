{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  - {t}Albums{/t} >
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

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        {acl isAllowed="ALBUM_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/checkbox.tpl" title="{t}Published{/t}" field="content_status"}
        {/acl}
        {include file="ui/component/content-editor/accordion/allow_comments.tpl"}
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
        <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
      </div>
      {include file="ui/component/content-editor/accordion/image.tpl" title="{t}Cover image{/t}" field="cover_image"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" title="{t}Agency{/t}" field="agency"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true counter=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>{t}Album images{/t}</h4>
    </div>
    <div class="grid-body">
      <div ui-sortable="{ axis: 'x,y', placeholder: 'album-thumbnail-sortable' }" ng-model="item.photos">
        <div class="album-thumbnail-sortable" ng-repeat="(index, photo) in item.photos">
          <div class="thumbnail-wrapper row">
            <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay['photo_'+ $index] }"></div>
            <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay['photo_'+ $index] }">
              <p>{t}Are you sure?{/t}</p>
              <div class="confirm-actions">
                <button class="btn btn-link" ng-click="toggleOverlay('photo_'+ $index)" type="button">
                  <i class="fa fa-times fa-lg"></i>
                  {t}No{/t}
                </button>
                <button class="btn btn-link" ng-click="removeItem('photos', $index);toggleOverlay('photo_'+ $index)" type="button">
                  <i class="fa fa-check fa-lg"></i>
                  {t}Yes{/t}
                </button>
              </div>
            </div>
            <div class="col-xs-3">
              <span class="sort-icon"></span>
              <div class="dynamic-image-placeholder">
                <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.photos[photo.pk_photo]" transform="zoomcrop,200,200">
                  <div class="thumbnail-actions">
                    <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo_'+ $index)">
                      <i class="fa fa-trash-o fa-2x"></i>
                    </div>
                  </div>
                </dynamic-image>
              </div>
            </div>
            <div class="col-xs-9">
              <div class="form-group no-margin">
                <textarea class="album-thumbnail-description form-control" ng-model="photo.description" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.photos[index].description[data.extra.locale.default] : '' %]" uib-tooltip="{t}Original{/t}: [% data.item.photos[index].description[data.extra.locale.default] %]" tooltip-enable="config.locale.multilanguage && config.locale.default !== config.locale.selected"></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="album-thumbnail-placeholder">
        <div class="img-thumbnail">
          <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="150" media-picker-target="new_photo">
            <i class="fa fa-plus fa-3x"></i>
            <h4>{t}Add images{/t}<h4>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-edit-album-error">
    {include file="album/modals/_edit_album_error.tpl"}
  </script>
{/block}
