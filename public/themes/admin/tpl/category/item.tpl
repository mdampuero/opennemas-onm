{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="CategoryCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id})">
    <div class="page-navbar actions-navbar ng-cloak" ng-show="!loading">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bookmark m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_categories_list') %]">
                  {t}Categories{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks m-l-5 m-r-5 ng-cloak">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>
                {if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}
              </h4>
            </li>
            <li class="quicklinks m-l-5 m-r-5 ng-cloak" ng-if="data.extra.locale.multilanguage">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks ng-cloak" ng-if="config.locale.multilanguage">
              <translator item="data.item" keys="data.extra.keys" ng-model="config.locale.selected" options="config.locale"></translator>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" ng-disabled="form.$invalid" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
        <div class="text-center p-b-15 p-t-15">
          <a href="[% routing.generate('backend_categories_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t}Unable to find the item{/t}</h3>
            <h4>{t}Click here to return to the list{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-md-4 col-md-push-8">
            <div class="grid simple">
              <div class="grid-body no-padding">
                <div class="grid-collapse-title">
                  <div class="checkbox">
                    <input type="checkbox" id="inmenu" ng-model="item.inmenu" ng-true-value="1">
                    <label for="inmenu" class="form-label">{t}Enabled{/t}</label>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.rss }" ng-click="expanded.rss = !expanded.rss">
                  <i class="fa fa-feed m-r-10"></i>{t}RSS{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.rss }"></i>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.rss }">
                  <div class="form-group no-margin">
                    <div class="checkbox">
                      <input type="checkbox" id="inrss" name="inrss" ng-false-value="'0'" ng-model="item.params.inrss" ng-true-value="'1'">
                      <label for="inrss" class="form-label">{t}Show in RSS{/t}</label>
                    </div>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.category }" ng-click="expanded.category = !expanded.category">
                  <i class="fa fa-bookmark m-r-10"></i>{t}Category{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category }">
                  <div class="form-group no-margin">
                    <onm-category-selector class="block" default-value-text="{t}Select a category{/t}..." exclude="[ item.pk_content_category ]" locale="config.locale.selected" ng-model="item.fk_content_category" placeholder="{t}Select a category{/t}"></onm-category-selector>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.color }" ng-click="expanded.color = !expanded.color">
                  <i class="fa fa-paint-brush m-r-10"></i>{t}Color{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.color }"></i>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.color }">
                  <div class="form-group no-margin">
                    <div class="controls">
                      {include file="ui/component/input/color.tpl" ngModel="item.color"}
                    </div>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.logo }" ng-click="expanded.logo = !expanded.logo">
                  <i class="fa fa-picture-o m-r-10"></i>{t}Logo{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.logo }"></i>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.logo }">
                  <div class="thumbnail-wrapper">
                    <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.cover }"></div>
                    <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.cover }">
                      <p>Are you sure?</p>
                      <div class="confirm-actions">
                        <button class="btn btn-link" ng-click="toggleOverlay('cover')" type="button">
                          <i class="fa fa-times fa-lg"></i>
                          {t}No{/t}
                        </button>
                        <button class="btn btn-link" ng-click="removeImage('cover');toggleOverlay('cover')" type="button">
                          <i class="fa fa-check fa-lg"></i>
                          {t}Yes{/t}
                        </button>
                      </div>
                    </div>
                    <div class="thumbnail-placeholder">
                      <div class="img-thumbnail" ng-show="!cover">
                        <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover">
                          <i class="fa fa-picture-o fa-2x"></i>
                          <h5>Pick an image</h5>
                        </div>
                      </div>
                      <div class="dynamic-image-placeholder" ng-show="cover">
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="cover">
                          <div class="thumbnail-actions">
                            <div class="thumbnail-action remove-action" ng-click="toggleOverlay('cover')">
                              <i class="fa fa-trash-o fa-2x"></i>
                            </div>
                            <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="cover" media-picker-types="photo">
                              <i class="fa fa-camera fa-2x"></i>
                            </div>
                          </div>
                        </dynamic-image>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-md-pull-4">
            <div class="grid simple">
              <div class="grid-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="title" class="form-label">
                        {t}Title{/t}
                      </label>
                      <div class="controls input-with-icon right">
                        <input class="form-control" id="title" name="title" ng-blur="generate()" ng-model="item.title" type="text" required uib-tooltip="[% data.item.title[data.extra.locale.default] %]" tooltip-enable="data.extra.locale.default !== config.locale.selected">
                        <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                          <span class="fa fa-check text-success" ng-if="form.title.$dirty && form.title.$valid"></span>
                          <span class="fa fa-info-circle text-info" ng-if="!form.title.$dirty && form.title.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                          <span class="fa fa-times text-error" ng-if="form.title.$dirty && form.title.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name" class="form-label">{t}Slug{/t}</label>
                      <div class="controls input-with-icon right">
                        <input class="form-control" id="name" name="name" ng-model="item.name" required type="text" uib-tooltip="[% data.item.name[data.extra.locale.default] %]" tooltip-enable="data.extra.locale.default !== config.locale.selected">
                        <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                          <span class="fa fa-circle-o-notch fa-spin" ng-if="flags.http.slug"></span>
                          <span class="fa fa-check text-success" ng-if="!flags.http.slug && form.name.$dirty && form.name.$valid"></span>
                          <span class="fa fa-info-circle text-info" ng-if="!flags.http.slug && !form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is required{/t}"></span>
                          <span class="fa fa-times text-error" ng-if="!flags.http.slug && form.name.$dirty && form.name.$invalid" uib-tooltip="{t}This field is invalid{/t}"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group no-margin">
                  <label class="form-label" for="description">
                    {t}Description{/t}
                  </label>
                  <div class="controls">
                    <textarea onm-editor onm-editor-preset="simple" ng-model="item.description" name="description" cols="30" rows="10"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
