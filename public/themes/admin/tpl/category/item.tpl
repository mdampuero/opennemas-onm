{extends file="common/extension/item.tpl"}

{block name="metaTitle"}
  > {t}Categories{/t} >
  {if empty($id)}
    {t}Create{/t}
  {else}
    {t}Edit{/t} ({$id})
  {/if}
{/block}

{block name="ngInit"}
  ng-controller="CategoryCtrl" ng-init="forcedLocale = '{$locale}'; getItem({$id}); flags.block.slug = true"
{/block}

{block name="icon"}
  <i class="fa fa-bookmark m-r-10"></i>
{/block}

{block name="title"}
  <a class="no-padding" href="[% routing.generate('backend_categories_list') %]">
    {t}Categories{/t}
  </a>
{/block}

{block name="rightColumn"}
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-collapse-title">
        <div class="form-group no-margin">
          <div class="checkbox">
            <input type="checkbox" id="enabled" ng-model="item.enabled" ng-true-value="1">
            <label for="enabled" class="form-label">{t}Enabled{/t}</label>
          </div>
        </div>
        <div class="form-group no-margin">
          <div class="checkbox m-t-5">
            <input type="checkbox" id="archived" ng-model="item.archived" ng-true-value="1">
            <label for="archived" class="form-label">{t}Archived{/t}</label>
          </div>
          <span class="help m-l-3 m-t-5" ng-if="isHelpEnabled()">
            <i class="fa fa-info-circle m-r-5 text-info"></i>
            {t}If enabled, category will not be included in category selectors nor RSS feeds{/t}
          </span>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.category }" ng-click="expanded.category = !expanded.category">
        <i class="fa fa-bookmark m-r-10"></i>{t}Subsection of{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
        <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!expanded.category && item.fk_content_category">
          [% data.extra.selected.title %]
        </span>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category }">
        <div class="form-group no-margin">
          <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" exclude="[ item.pk_content_category ]" export-model="data.extra.selected" show-archived="true" locale="config.locale.selected" ng-model="item.fk_content_category" placeholder="{t}Select a category{/t}…"></onm-category-selector>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.color }" ng-click="expanded.color = !expanded.color">
        <i class="fa fa-paint-brush m-r-10"></i>{t}Color{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.color }"></i>
        <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase" ng-style="{ 'background-color': item.color }" ng-show="!expanded.color && item.color">
          &nbsp;&nbsp;
        </span>
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
              <dynamic-image autoscale="true" class="img-thumbnail" instance="{$app.instance->getMediaShortPath()}/" ng-model="cover">
                <div class="thumbnail-actions">
                  <div class="thumbnail-action remove-action" ng-click="toggleOverlay('cover')">
                    <i class="fa fa-trash-o fa-2x"></i>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-md-4 col-md-push-8">
            <div class="grid simple">
              <div class="grid-body no-padding">
                <div class="grid-collapse-title">
                  <div class="form-group no-margin">
                    <div class="checkbox">
                      <input type="checkbox" id="enabled" ng-model="item.enabled" ng-true-value="1">
                      <label for="enabled" class="form-label">{t}Enabled{/t}</label>
                    </div>
                  </div>
                  <div class="form-group no-margin">
                    <div class="checkbox m-t-5">
                      <input type="checkbox" id="archived" ng-model="item.archived" ng-true-value="1">
                      <label for="archived" class="form-label">{t}Archived{/t}</label>
                    </div>
                    <span class="help m-l-3 m-t-5" ng-if="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}If enabled, category will not be included in category selectors nor RSS feeds{/t}
                    </span>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.category }" ng-click="expanded.category = !expanded.category">
                  <i class="fa fa-bookmark m-r-10"></i>{t}Subsection of{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-bold text-uppercase" ng-show="!expanded.category && item.fk_content_category">
                    [% data.extra.selected.title %]
                  </span>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category }">
                  <div class="form-group no-margin">
                    <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" exclude="[ item.id ]" export-model="data.extra.selected" show-archived="true" locale="config.locale.selected" ng-model="item.fk_content_category" placeholder="{t}Select a category{/t}…"></onm-category-selector>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.color }" ng-click="expanded.color = !expanded.color">
                  <i class="fa fa-paint-brush m-r-10"></i>{t}Color{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.color }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase" ng-style="{ 'background-color': item.color }" ng-show="!expanded.color && item.color">
                    &nbsp;&nbsp;
                  </span>
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
                        <dynamic-image autoscale="true" class="img-thumbnail" instance="{$app.instance->getMediaShortPath()}/" ng-model="cover">
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
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.menu }" ng-click="expanded.menu = !expanded.menu" ng-show="data.extra.menu">
                  <i class="fa fa-list-alt m-r-10"></i>{t}Menu{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.menu }"></i>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.menu }" ng-show="data.extra.menu">
                  <div class="form-group no-margin">
                    <div class="controls">
                      <onm-menu-selector class="block" ng-model="item.params.menu" default-value-text="{t}Default{/t}" placeholder="{t}Default{/t}" label-text></onm-menu-selector>
                    </div>
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
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.menu }" ng-click="expanded.menu = !expanded.menu" ng-show="data.extra.menu">
        <i class="fa fa-list-alt m-r-10"></i>{t}Menu{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.menu }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.menu }" ng-show="data.extra.menu">
        <div class="form-group no-margin">
          <div class="controls">
            <onm-menu-selector class="block" ng-model="item.params.menu" default-value-text="{t}Default{/t}" placeholder="{t}Default{/t}" label-text></onm-menu-selector>
          </div>
        </div>
      </div>
      <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.type }" ng-click="expanded.type = !expanded.type" ng-show="data.extra.types && data.extra.types.length > 0">
        <i class="fa fa-magic m-r-10"></i>{t}Type{/t}
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.type }"></i>
      </div>
      <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.type }" ng-show="data.extra.types && data.extra.types.length > 0">
        <div class="form-group no-margin">
          <div class="controls">
            <ui-select class="block" name="activated" theme="select2" ng-model="item.params.type">
              <ui-select-match>
                [% $select.selected.name %]
              </ui-select-match>
              <ui-select-choices repeat="item.id as item in addEmptyValue(data.extra.types, 'id', 'name', '{t}Default{/t}') | filter: $select.search">
                <div ng-bind-html="item.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </div>
        </div>
      </div>
    </div>
  </div>
{/block}

{block name="leftColumn"}
  <div class="grid simple">
    <div class="grid-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="title" class="form-label">
              {t}Title{/t}
            </label>
            <div class="controls input-with-icon right">
              <input class="form-control" id="title" name="title" ng-blur="generate()" ng-model="item.title" placeholder="[% config.locale.multilanguage && config.locale.default !== config.locale.selected ? data.item.title[config.locale.default] : '' %]" required type="text" uib-tooltip="[% data.item.title[data.extra.locale.default] %]" tooltip-enable="data.extra.locale.default !== config.locale.selected">
              <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                <span class="fa fa-check text-success" ng-if="form.title.$dirty && form.title.$valid"></span>
                <span class="fa fa-info-circle text-info" ng-if="!form.title.$dirty && form.title.$invalid" tooltip-class="tooltip-info" uib-tooltip="{t}This field is required{/t}"></span>
                <span class="fa fa-times text-error" ng-if="form.title.$dirty && form.title.$invalid" tooltip-class="tooltip-danger" uib-tooltip="{t}This field is invalid{/t}"></span>
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="name" class="form-label">{t}Slug{/t}</label>
            <div class="controls">
              <div class="input-group">
                <div class="input-group-btn">
                  <button class="btn btn-default" ng-click="flags.block.slug = !flags.block.slug" type="button">
                    <i class="fa" ng-class="{ 'fa-lock': flags.block.slug, 'fa-unlock-alt': !flags.block.slug }"></i>
                  </button>
                </div>
                <div class="input-with-icon right">
                  <input class="form-control" id="name" name="name" ng-disabled="flags.block.slug" ng-model="item.name" placeholder="[% config.locale.multilanguage && config.locale.default !== config.locale.selected ? data.item.name[config.locale.default] : '' %]" required type="text" uib-tooltip="[% data.item.name[data.extra.locale.default] %]" tooltip-enable="data.extra.locale.default !== config.locale.selected">
                  <span class="icon right ng-cloak" ng-if="!flags.http.loading">
                    <span class="fa fa-circle-o-notch fa-spin" ng-if="flags.http.slug"></span>
                    <span class="fa fa-check text-success" ng-if="!flags.http.slug && form.name.$dirty && form.name.$valid"></span>
                    <span class="fa fa-info-circle text-info" ng-if="!flags.http.slug && !form.name.$dirty && form.name.$invalid" tooltip-class="tooltip-info" uib-tooltip="{t}This field is required{/t}"></span>
                    <span class="fa fa-times text-error" ng-if="!flags.http.slug && form.name.$dirty && form.name.$invalid" tooltip-class="tooltip-danger" uib-tooltip="{t}This field is invalid{/t}"></span>
                  </span>
                </div>
              </div>
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
{/block}
