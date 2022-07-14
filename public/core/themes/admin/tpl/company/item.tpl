{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Companies{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="CompanyCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id});"
{/block}

{block name="icon"}
  <i class="fa fa-building m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="{url name=backend_companies_list}">
    {t}Companies{/t}
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
        <a class="btn btn-link" ng-click="expansibleSettings()" title="{t 1=_('Company')}Config form: '%1'{/t}">
          <span class="fa fa-cog fa-lg"></span>
        </a>
      </li>
      <li class="quicklinks">
        <button class="btn btn-white m-r-5" id="preview-button" ng-click="preview()" type="button" id="preview_button">
          <i class="fa fa-desktop" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.generating_preview }" ></i>
          {t}Preview{/t}
        </button>
      </li>
      <li class="quicklinks">
        <button class="btn btn-loading btn-success text-uppercase" ng-click="submit()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
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
        {acl isAllowed="COMPANY_AVAILABLE"}
          {include file="ui/component/content-editor/accordion/published.tpl"}
        {/acl}
      </div>
      {include file="ui/component/content-editor/accordion/tags.tpl"}
      {include file="ui/component/content-editor/accordion/slug.tpl" iRoute="[% getFrontendUrl(item) %]"}
      {include file="ui/component/content-editor/accordion/scheduling.tpl"}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <i class="fa fa-cog m-r-10"></i> {t}Parameters{/t}
      </div>
      {include file="ui/component/content-editor/accordion/sector.tpl" required=true}
      {include file="ui/component/content-editor/accordion/schedule.tpl" field="schedule" icon="fa-calendar-o"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="facebook" icon="fa-facebook" title="{t}Facebook{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="twitter" icon="fa-twitter" title="{t}Twitter{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="instagram" icon="fa-instagram" title="{t}Instagram{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="whatsapp" icon="fa-whatsapp" title="{t}Whatsapp{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="phone" icon="fa-phone" title="{t}Phone{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="email" icon="fa-envelope" title="{t}Email{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="address" icon="fa-map-pin" title="{t}Address{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="maps" icon="fa-map-marker" title="{t}Google Maps{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="website" icon="fa-globe" title="{t}Website{/t}"}
      {include file="common/component/related-contents/_featured-media.tpl" iName="logo" iTitle="{t}Logo{/t}" types="photo"}
      {include file="common/component/related-contents/_featured-media.tpl" iName="featuredInner" iTitle="{t}Featured in inner{/t}" types="photo,video,album"}
      {include file="common/component/related-contents/_related-content.tpl" iName="relatedInner" iTitle="{t}Related in inner{/t}"}
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
      {include file="ui/component/input/text.tpl" iCounter=true iField="pretitle" iTitle="{t}Pretitle{/t}"}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}
      {include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" preset="standard" rows=15 imagepicker=true contentPicker=true}
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>
        <i class="fa fa-cart-arrow-down m-r-5"></i>
        {t}Products{/t}
      </h4>
      <div class="pull-right">
        <button class="btn btn-link no-padding p-t-5 m-r-10" ng-click="setMode(app.mode === 'grid' ? 'list' : 'grid')" type="button">
          <i class="fa" ng-class="{ 'fa-th': app.mode === 'grid', 'fa-list': app.mode === 'list' }"></i>
        </button>
      </div>
    </div>
    <div class="grid-body">
      <div class="row">
        <div class="col-md-12">
          <div ui-tree="treeOptions">
            <div class="album-photos album-photos-[% app.mode %]" ng-model="photos" ui-tree-nodes="">
              <div class="album-photos-item" ng-repeat="photo in photos" ui-tree-node>
                <span ui-tree-handle>
                  <span class="angular-ui-tree-icon"></span>
                </span>
                <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay['photo_'+ photo.target_id] }"></div>
                <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay['photo_'+ photo.target_id] }">
                  <p>{t}Are you sure?{/t}</p>
                  <div class="confirm-actions">
                    <button class="btn btn-link" ng-click="toggleOverlay('photo_'+ photo.target_id)" type="button">
                      <i class="fa fa-times fa-lg"></i>
                      {t}No{/t}
                    </button>
                    <button class="btn btn-link" ng-click="removeItem('photos', $index); removeItem('data.photos', $index); toggleOverlay('photo_'+ photo.target_id)" type="button">
                      <i class="fa fa-check fa-lg"></i>
                      {t}Yes{/t}
                    </button>
                  </div>
                </div>
                <div class="thumbnail-wrapper row">
                  <div ng-class="{ 'col-lg-2 col-sm-3': app.mode === 'list', 'col-xs-12': app.mode === 'grid' }">
                    <div class="dynamic-image-placeholder">
                      <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="data.extra.related_contents[photo.target_id]" transform="zoomcrop,200,200">
                        <div class="thumbnail-actions thumbnail-actions-1x">
                          <div class="thumbnail-action remove-action" ng-click="toggleOverlay('photo_'+ photo.target_id)">
                            <i class="fa fa-trash-o fa-2x"></i>
                          </div>
                        </div>
                      </dynamic-image>
                    </div>
                  </div>
                  <div ng-class="{ 'col-lg-10 col-sm-9': app.mode === 'list', 'col-xs-12': app.mode === 'grid' }">
                    <div class="form-group no-margin">
                      <textarea class="album-thumbnail-description form-control" ng-model="photo.caption" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.photos[index].description[data.extra.locale.default] : '' %]"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <input name="photos" ng-model="value.photos" type="hidden">
        </div>
      </div>
      <div class="text-center">
        <button class="btn btn-default" media-picker media-picker-ignore="[% related.getIds('photos') %]" media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="50" media-picker-target="target.photos" media-picker-types="photo" type="button">
          <i class="fa fa-plus m-r-5"></i>
          {t}Add{/t}
        </button>
        <button class="btn btn-white" ng-click="empty()" ng-if="photos && photos.length > 0" type="button">
          <i class="fa fa-fire m-r-5"></i>
          {t}Empty{/t}
        </button>
      </div>
    </div>
  </div>
{/block}

{block name="modals"}
  <script type="text/ng-template" id="modal-preview">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()" type="button">&times;</button>
      <h4 class="modal-title">
        {t}Preview{/t}
      </h4>
    </div>
    <div class="modal-body clearfix no-padding">
      <iframe ng-src="[% template.src %]" frameborder="0"></iframe>
    </div>
  </script>
  <script type="text/ng-template" id="modal-delete">
    {include file="common/extension/modal.delete.tpl"}
  </script>
  <script type="text/ng-template" id="modal-translate">
    {include file="common/modals/_translate.tpl"}
  </script>
  <script type="text/ng-template" id="modal-draft">
    {include file="common/modals/_draft.tpl"}
  </script>
  <script type="text/ng-template" id="modal-expansible-fields">
    {include file="common/modals/_modalExpansibleFields.tpl"}
  </script>
{/block}

