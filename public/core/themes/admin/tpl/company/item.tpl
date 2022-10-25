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
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.company_info = !expanded.company_info">
        <i class="fa fa-pie-chart m-r-10"></i>{t}Company info{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.company_info }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.company_info }">

        <div class="form-group ">
          <i class="fa fa-address-card m-r-10"></i>{t}CIF{/t}
          <div class="controls">
            <input class="form-control" id="cif" name="cif" ng-model="item.cif" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-location-arrow m-r-10"></i>{t}Postal code{/t}
          <div class="controls">
            <input class="form-control" id="postal_code" name="postal_code" ng-model="item.postal_code" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-map-pin m-r-10"></i>{t}Address{/t}
          <div class="controls">
            <input class="form-control" id="address" name="address" ng-model="item.address" type="text"/>
          </div>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.province = !expanded.province">
        <i class="fa fa-map-signs m-r-10"></i>{t}Province{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.province }"></i>
        <span class="pull-right" ng-if="!expanded.province && item.province">
          <span class="form-status">
              <span class="form-status-item">
                <span class="ng-cloak badge badge-default">
                  <strong>
                    [% item.province ? item.province.nm : '' %]
                  </strong>
                </span>
              </span>
          </span>
        </span>
        <span class="pull-right" ng-if="!expanded.province">
          <span class="form-status">
            <span class="form-status-item" ng-class="{ 'has-info': !item.province }">
              <span class="fa fa-check text-success" ng-if="item.province"></span>
              <span class="fa fa-info-circle text-info" ng-if="!item.province" tooltip-class="tooltip-right" uib-tooltip="{t}This field is required{/t}"></span>
            </span>
          </span>
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.province }">
        <div class="form-group no-margin">
          <div class="controls">
            <select name="contactCountry" ng-model="item.province" ng-options="item.nm for item in provinces" required >
              <option value="">{t}Select a province{/t}...</option>
            </select>
          </div>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.locality = !expanded.locality" ng-if="item.province">
        <i class="fa fa-map-signs m-r-10"></i>{t}Locality{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.locality }"></i>
        <span class="pull-right" ng-if="!expanded.locality && item.locality">
          <span class="form-status">
              <span class="form-status-item">
                <span class="ng-cloak badge badge-default">
                  <strong>
                    [% item.locality ? item.locality.nm : '' %]
                  </strong>
                </span>
              </span>
          </span>
        </span>
        <span class="pull-right" ng-if="!expanded.locality">
          <span class="pull-right" ng-if="!expanded.locality && item.province">
            <span class="form-status">
              <span class="form-status-item" ng-class="{ 'has-info': !item.locality && item.province }">
                <span class="fa fa-check text-success" ng-if="item.locality && item.province"></span>
                <span class="fa fa-info-circle text-info" ng-if="!item.locality && item.province" tooltip-class="tooltip-right" uib-tooltip="{t}This field is required{/t}"></span>
              </span>
            </span>
          </span>
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.locality }" ng-if="item.province">
        <div class="form-group no-margin">
          <div class="controls">
            <select name="contactCountry" ng-model="item.locality" ng-options="item.nm for item in filteredLocalities" ng-required="item.province">
              <option value="">{t}Select a locality{/t}...</option>
            </select>
          </div>
        </div>
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
      <div ng-if="extraFields">
        <div ng-repeat="element in extraFields track by $index">
          <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded[element.key.value] = !expanded[element.key.value]">
            <i class="fa fa-pie-chart m-r-10"></i>{t}[% element.key.name %]{/t}
            <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded[element.key.value] }"></i>
          </div>
          <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded[element.key.value] }">
            <div class="form-group no-margin">
              <div class="controls">
                <div class="tags-input-wrapper">
                  <tags-input display-property="name" use-strings="true" replace-spaces-with-dashes="false" on-tag-adding="checkTag($tag)" key-property="name" min-length="1" ng-model="item[element.key.value]"  add-from-autocomplete-only="true" placeholder="{t}Add an element...{/t}">
                    <auto-complete source="list($query,element.values)" load-on-down-arrow="true" min-length="2" select-first-match="true" debounce-delay="250"></auto-complete>
                  </tags-input>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.social_media = !expanded.social_media">
        <i class="fa fa-pie-chart m-r-10"></i>{t}Social network{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.social_media }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.social_media }">
        <div class="form-group ">
          <i class="fa fa-facebook m-r-10"></i>{t}Facebook{/t}
          <div class="controls">
            <input class="form-control" id="facebook" name="facebook" ng-model="item.facebook" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-twitter m-r-10"></i>{t}Twitter{/t}
          <div class="controls">
            <input class="form-control" id="twitter" name="twitter" ng-model="item.twitter" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-instagram m-r-10"></i>{t}Instagram{/t}
          <div class="controls">
            <input class="form-control" id="instagram" name="instagram" ng-model="item.instagram" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-whatsapp m-r-10"></i>{t}Whatsapp{/t}
          <div class="controls">
            <input class="form-control" id="whatsapp" name="whatsapp" ng-model="item.whatsapp" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-linkedin m-r-10"></i>{t}Linkedin{/t}
          <div class="controls">
            <input class="form-control" id="linkedin" name="linkedin" ng-model="item.linkedin" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-youtube m-r-10"></i>{t}Youtube{/t}
          <div class="controls">
            <input class="form-control" id="youtube" name="youtube" ng-model="item.youtube" type="text"/>
          </div>
        </div>
        <div class="form-group ">
          <i class="fa fa-music m-r-10"></i>{t}TikTok{/t}
          <div class="controls">
            <input class="form-control" id="tiktok" name="tiktok" ng-model="item.tiktok" type="text"/>
          </div>
        </div>
      </div>
      {include file="ui/component/content-editor/accordion/schedule.tpl" field="schedule" icon="fa-calendar-o"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="phone" icon="fa-phone" title="{t}Phone{/t}"}
      {include file="ui/component/content-editor/accordion/input-text.tpl" field="email" icon="fa-envelope" title="{t}Email{/t}"}
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
      {include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Company Name{/t}" iValidation=true}
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

