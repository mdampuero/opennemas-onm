{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="ThemeSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-eye fa-lg"></i>
                {t}Theme settings{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
            {acl isAllowed="MASTER"}
              <li class="quicklinks">
                <a class="btn btn-white" ng-click="openRestoreModal()">
                  <span class="fa fa-undo"></span>
                  {t}Restore{/t}
                </a>
              </li>
              <li class="quicklinks">
                <a class="btn btn-white" ng-click="openImportModal()">
                  <span class="fa fa-sign-in"></span>
                  {t}Import{/t}
                </a>
              </li>
              <li class="quicklinks">
                <a class="btn btn-white" href="[% routing.generate('api_v1_backend_settings_theme_download') %]">
                  <span class="fa fa-download"></span>
                  {t}Download{/t}
                </a>
              </li>
            {/acl}
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="save()" ng-disabled="settingForm.$invalid" type="button">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving}"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content ng-cloak no-animate" ng-if="loading">
      <div class="spinner-wrapper">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
    </div>
    <div class="content ng-cloak" ng-if="!loading">
      <div class="grid simple settings">
        <div class="grid-body ng-cloak">
          {if $app.security->hasPermission('MASTER')}
            <div class="row">
              <div class="col-xs-12">
                <div ng-if="extra.theme_skins.length !== 0">
                  <div class="row m-b-15">
                    <div class="col-xs-12">
                      <label class="form-label" for="theme-style">
                        <h4>
                          <i class="fa fa-paint-brush"></i>
                          {t}Default skin{/t}
                        </h4>
                        <span class="help">
                          {t}Your theme offers multiple skins to slightly change your theme. Select which one do you want.{/t}
                        </span>
                      </label>
                      <div class="controls">
                        <div class="input-group">
                          <select id="theme-style" name="theme-style" ng-model="settings.theme_skin" required>
                            <option value="[% code %]" ng-repeat="(code,style) in extra.theme_skins" ng-selected="[% code === settings.theme_skin || settings.theme_skin == undefined %]">[% style.name %]</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                <ul class="nav nav-pills border-bottom border-light m-b-15 p-b-5" role="tablist">
                  <li role="presentation" class="p-l-0 active">
                    <a href="#tabGeneral" aria-controls="general" role="tab" data-toggle="tab">{t domain="base"}General{/t}</a>
                  </li>
                  <li role="presentation" class="p-l-0" ng-if="extra.theme_skins[settings.theme_skin].params.fonts">
                    <a href="#tabFonts" aria-controls="fonts" role="tab" data-toggle="tab">{t domain="base"}Fonts{/t}</a>
                  </li>
                  <li role="presentation" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                    <a href="#tabHeader" aria-controls="profile" role="tab" data-toggle="tab">{t domain="base"}Header{/t}</a>
                  </li>
                  <li role="presentation" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                    <a href="#tabMenu" aria-controls="menu" role="tab" data-toggle="tab">{t domain="base"}Menu{/t}</a>
                  </li>
                  <li role="presentation" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                    <a href="#tabFrontpage" aria-controls="frontpage" role="tab" data-toggle="tab">{t domain="base"}Frontpage{/t}</a>
                  </li>
                  <li role="presentation" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                    <a href="#tabArchive" aria-controls="archive" role="tab" data-toggle="tab">{t domain="base"}Listings/Archive{/t}</a>
                  </li>
                  <li role="presentation" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                    <a href="#tabInners" aria-controls="inners" role="tab" data-toggle="tab">{t domain="base"}Inners{/t}</a>
                  </li>
                  <li role="presentation" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                    <a href="#tabMobile" aria-controls="mobile" role="tab" data-toggle="tab">{t domain="base"}Mobile{/t}</a>
                  </li>
                </ul>

                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane fade in active" id="tabGeneral">
                    <div class="row">
                      <h4>
                        <i class="fa fa-paint-brush"></i>
                        {t}Colors{/t}
                      </h4>
                      <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                          <label class="form-label" for="site-color">
                            {t}Site color{/t}
                          </label>
                          <span class="help">
                            {t}Color used for links, menus and some widgets.{/t}
                          </span>
                          <div class="controls">
                            <div class="input-group">
                              <span class="input-group-addon" ng-style="{ 'background-color': settings.site_color }">
                                &nbsp;&nbsp;&nbsp;&nbsp;
                              </span>
                              <input class="form-control" colorpicker="hex" id="site-color" name="site-color" ng-model="settings.site_color" type="text">
                              <div class="input-group-btn">
                                <button class="btn btn-default" ng-click="settings.site_color = backup.site_color" type="button">{t}Reset{/t}</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                          <label class="form-label" for="site-color-secondary">
                            {t}Site secondary color{/t}
                          </label>
                          <span class="help">
                            {t}Color used for custom elements.{/t}
                          </span>
                          <div class="controls">
                            <div class="input-group">
                              <span class="input-group-addon" ng-style="{ 'background-color': settings.site_color_secondary }">
                                &nbsp;&nbsp;&nbsp;&nbsp;
                              </span>
                              <input class="form-control" colorpicker="hex" id="site-color-secondary" name="site-color-secondary" ng-model="settings.site_color_secondary" type="text">
                              <div class="input-group-btn">
                                <button class="btn btn-default" ng-click="settings.site_color_secondary = backup.site_color_secondary" type="button">{t}Reset{/t}</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-5">
                        <h4>
                          <i class="fa fa-picture-o"></i>
                          {t}Logo{/t}
                        </h4>
                        <div class="form-group">
                          <div class="checkbox">
                            <input class="form-control" id="logo-enabled" name="logo-enabled" ng-false-value="0" ng-model="settings.logo_enabled" ng-true-value="1"  type="checkbox"/>
                            <label class="form-label" for="logo-enabled">
                              {t}Use custom logo{/t}
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-5 m-b-15" ng-if="settings.logo_enabled && extra.theme_skins[settings.theme_skin].params.options">
                        <h4>
                          <i class="fa fa-arrows-h"></i>
                          {t}Logo size{/t}
                        </h4>
                        <label class="form-label" for="main-logo-size">
                          <span class="help">
                            {t}Choose header default size{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="main-logo-size" name="main-logo-size" ng-model="settings.theme_options.main_logo_size">
                              <option value="[% main_logo_size_name %]" ng-selected="main_logo_size_name == settings.theme_options.main_logo_size" ng-repeat="(main_logo_size_name,main_logo_size_value) in extra.theme_skins[settings.theme_skin].params.options.main_logo_size.options">[% main_logo_size_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-xs-12 col-md-5" ng-show="settings.logo_enabled">
                        <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.logo_default }"></div>
                        <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.logo_default }">
                          <p>{t}Are you sure?{/t}</p>
                          <div class="confirm-actions">
                            <button class="btn btn-link" ng-click="toggleOverlay('logo_default')" type="button">
                              <i class="fa fa-times fa-lg"></i>
                              {t}No{/t}
                            </button>
                            <button class="btn btn-link" ng-click="removeFile('logo_default'); toggleOverlay('logo_default')" type="button">
                              <i class="fa fa-check fa-lg"></i>
                              {t}Yes{/t}
                            </button>
                          </div>
                        </div>
                        <label class="form-label" for="site-logo">{t}Large logo{/t}</label>
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!settings.logo_default">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_default">
                              <i class="fa fa-picture-o fa-2x"></i>
                              <h5>{t}Pick an image{/t}</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder dynamic-image-no-margin ng-cloak" ng-if="settings.logo_default">
                            <dynamic-image reescale="true" class="img-thumbnail " instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="settings.logo_default" ng-if="settings.logo_default" only-image="true">
                              <div class="thumbnail-actions ng-cloak">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('logo_default')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_default">
                                  <i class="fa fa-camera fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_default" media-picker-type="photo" ></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                      <div class="form-group col-xs-12 col-md-5" ng-if="settings.logo_enabled">
                        <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.logo_simple }"></div>
                        <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.logo_simple }">
                          <p>{t}Are you sure?{/t}</p>
                          <div class="confirm-actions">
                            <button class="btn btn-link" ng-click="toggleOverlay('logo_simple')" type="button">
                              <i class="fa fa-times fa-lg"></i>
                              {t}No{/t}
                            </button>
                            <button class="btn btn-link" ng-click="removeFile('logo_simple'); toggleOverlay('logo_simple')" type="button">
                              <i class="fa fa-check fa-lg"></i>
                              {t}Yes{/t}
                            </button>
                          </div>
                        </div>
                        <label class="form-label" for="logo_simple">{t}Small logo{/t}</label>
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!settings.logo_simple">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_simple">
                              <i class="fa fa-picture-o fa-2x"></i>
                              <h5>{t}Pick an image{/t}</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder dynamic-image-no-margin  ng-cloak " ng-if="settings.logo_simple">
                            <dynamic-image reescale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="settings.logo_simple" ng-if="settings.logo_simple" only-image="true">
                              <div class="thumbnail-actions ng-cloak">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('logo_simple')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_simple">
                                  <i class="fa fa-camera fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_simple" media-picker-type="photo" ></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                      <div class="form-group col-xs-12 col-md-5" ng-if="settings.logo_enabled">
                        <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.logo_favico }"></div>
                        <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.logo_favico }">
                          <p>{t}Are you sure?{/t}</p>
                          <div class="confirm-actions">
                            <button class="btn btn-link" ng-click="toggleOverlay('logo_favico')" type="button">
                              <i class="fa fa-times fa-lg"></i>
                              {t}No{/t}
                            </button>
                            <button class="btn btn-link" ng-click="removeFile('logo_favico'); toggleOverlay('logo_favico')" type="button">
                              <i class="fa fa-check fa-lg"></i>
                              {t}Yes{/t}
                            </button>
                          </div>
                        </div>
                        <label class="form-label" for="logo_favico">{t}Favico{/t}</label>
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!settings.logo_favico">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_favico">
                              <i class="fa fa-picture-o fa-2x"></i>
                              <h5>{t}Pick an image{/t}</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder dynamic-image-no-margin  ng-cloak " ng-if="settings.logo_favico">
                            <dynamic-image reescale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="settings.logo_favico" ng-if="settings.logo_favico" only-image="true">
                              <div class="thumbnail-actions ng-cloak">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('logo_favico')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_favico">
                                  <i class="fa fa-camera fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_favico" media-picker-type="photo" ></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                      <div class="form-group col-xs-12 col-md-5" ng-if="settings.logo_enabled">
                        <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.logo_embed }"></div>
                        <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.logo_embed }">
                          <p>{t}Are you sure?{/t}</p>
                          <div class="confirm-actions">
                            <button class="btn btn-link" ng-click="toggleOverlay('logo_embed')" type="button">
                              <i class="fa fa-times fa-lg"></i>
                              {t}No{/t}
                            </button>
                            <button class="btn btn-link" ng-click="removeFile('logo_embed'); toggleOverlay('logo_embed')" type="button">
                              <i class="fa fa-check fa-lg"></i>
                              {t}Yes{/t}
                            </button>
                          </div>
                        </div>
                        <label class="form-label" for="logo_embed">{t}Social network default image{/t}</label>
                        <div class="thumbnail-placeholder">
                          <div class="img-thumbnail" ng-if="!settings.logo_embed">
                            <div class="thumbnail-empty" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_embed">
                              <i class="fa fa-picture-o fa-2x"></i>
                              <h5>{t}Pick an image{/t}</h5>
                            </div>
                          </div>
                          <div class="dynamic-image-placeholder dynamic-image-no-margin ng-cloak " ng-if="settings.logo_embed">
                            <dynamic-image reescale="true" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="settings.logo_embed" ng-if="settings.logo_embed" only-image="true">
                              <div class="thumbnail-actions ng-cloak">
                                <div class="thumbnail-action remove-action" ng-click="toggleOverlay('logo_embed')">
                                  <i class="fa fa-trash-o fa-2x"></i>
                                </div>
                                <div class="thumbnail-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_embed">
                                  <i class="fa fa-camera fa-2x"></i>
                                </div>
                              </div>
                              <div class="thumbnail-hidden-action" media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="settings.logo_embed" media-picker-type="photo" ></div>
                            </dynamic-image>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row" ng-if="extra.theme_skins[settings.theme_skin].params.options">

                      <div class="col-xs-12 col-md-5 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-hamburger-position">
                          <h4>
                            <i class="fa fa-align-center"></i>
                            {t}Hamburger menu{/t}
                          </h4>
                          <span class="help">
                            {t}Choose where to appear menu{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.hamburger_position">
                            <div class="panel panel-default col-xs-5" ng-repeat="(hamburger_position_name,hamburger_position_value) in extra.theme_skins[settings.theme_skin].params.options.hamburger_position.options">
                              <div class="radio">
                                <input id="theme-option-hamburger-position-[% hamburger_position_name %]" name="theme-option-hamburger-position" ng-model="settings.theme_options.hamburger_position" value="[% hamburger_position_name %]" ng-checked="[% hamburger_position_name === settings.theme_options.hamburger_position %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-hamburger-position-[% hamburger_position_name %]">
                                  <img src="/themes/apolo/images/admin/hamburger_position-[% hamburger_position_name %].jpg" alt="[% hamburger_position_value %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% hamburger_position_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-arrows-h"></i>
                          {t}Page Layout{/t}
                        </h4>
                        <label class="form-label" for="theme-option-width">
                          <span class="help">
                            {t}Page containers width{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-option-width" name="theme-option-width" ng-model="settings.theme_options.general_page_width" required>
                              <option value="[% page_width_name %]" ng-repeat="(page_width_name,page_width_value) in extra.theme_skins[settings.theme_skin].params.options.general_page_width.options" ng-selected="[% page_width_name == settings.theme_options.general_page_width %]">[% page_width_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-12 col-md-8 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-widget-header-type">
                          <h4>
                            <i class="fa fa-minus"></i>
                            {t}Section headers{/t}
                          </h4>
                          <span class="help">
                            {t}Choose type{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.widget_header_type">
                            <div class="panel panel-default col-xs-5 col-md-4" ng-repeat="(widget_header_type_name,widget_header_type_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_type.options">
                              <div class="radio">
                                <input id="theme-option-widget-header-type-[% widget_header_type_name %]" name="theme-option-widget-header-type" ng-model="settings.theme_options.widget_header_type" value="[% widget_header_type_name %]" ng-checked="[% widget_header_type_name === settings.theme_options.widget_header_type %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-widget-header-type-[% widget_header_type_name %]">
                                  <img src="/themes/apolo/images/admin/widget-header-[% widget_header_type_name %].jpg" alt="[% widget_header_type_value %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% widget_header_type_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12">
                        <div class="row">
                          <div class="col-xs-12 col-md-4 m-b-15">
                            <label class="form-label" for="theme-option-widget-header-font">
                              <span class="help">
                                {t}Section header font{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-widget-header-font" name="theme-option-widget-header-font" ng-model="settings.theme_options.widget_header_font">
                                  <option value="[% widget_header_font_name %]" ng-repeat="(widget_header_font_name,widget_header_font_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_font.options" ng-selected="[% widget_header_font_name === settings.theme_options.widget_header_font %]">[% widget_header_font_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-4 m-b-15">
                            <label class="form-label" for="theme-widget-header-font-size">
                              <span class="help">
                                {t}Section header font size{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-widget-header-font-size" name="theme-widget-header-font-size" ng-model="settings.theme_options.widget_header_font_size">
                                  <option value="[% widget_header_font_size_name %]" ng-repeat="(widget_header_font_size_name,widget_header_font_size_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_font_size.options" ng-selected="[% widget_header_font_size_name === settings.theme_options.widget_header_main_font_size %]">[% widget_header_font_size_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-4 m-b-15">
                            <label class="form-label" for="theme-option-widget-header-font">
                              <span class="help">
                                {t}Section header font weight{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-widget-header-font-weight" name="theme-option-widget-header-font-weight" ng-model="settings.theme_options.widget_header_font_weight">
                                  <option value="[% widget_header_font_weight_name %]" ng-repeat="(widget_header_font_weight_name,widget_header_font_weight_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_font_weight.options" ng-selected="[% widget_header_font_weight_name === settings.theme_options.widget_header_font_weight %]">[% widget_header_font_weight_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-xs-12 col-md-4 m-b-15">
                            <label class="form-label" for="theme-option-widget-header-color">
                              <span class="help">
                                {t}Section header text color{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-widget-header-font-color" name="theme-option-widget-header-font-color" ng-model="settings.theme_options.widget_header_font_color">
                                  <option value="[% widget_header_font_color_name %]" ng-repeat="(widget_header_font_color_name,widget_header_font_color_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_font_color.options" ng-selected="[% widget_header_font_color_name === settings.theme_options.widget_header_font_color %]">[% widget_header_font_color_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-4 m-b-15">
                            <label class="form-label" for="theme-option-widget-border-position">
                              <span class="help">
                                {t}Section header border position{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-widget-header-border-position" name="theme-option-widget-header-border-position" ng-model="settings.theme_options.widget_header_border_position">
                                  <option value="[% widget_header_border_position_name %]" ng-repeat="(widget_header_border_position_name,widget_header_border_position_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_border_position.options" ng-selected="[% widget_header_border_position_name === settings.theme_options.widget_header_border_position %]">[% widget_header_border_position_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-4 m-b-15">
                            <label class="form-label" for="theme-option-widget-border-color">
                              <span class="help">
                                {t}Section header border color{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-widget-header-border-color" name="theme-option-widget-header-border-color" ng-model="settings.theme_options.widget_header_border_color">
                                  <option value="[% widget_header_border_color_name %]" ng-repeat="(widget_header_border_color_name,widget_header_border_color_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_border_color.options" ng-selected="[% widget_header_border_color_name === settings.theme_options.widget_header_border_color %]">[% widget_header_border_color_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.theme_options.widget_header_type === 'type-b' || settings.theme_options.widget_header_type === 'type-c'">
                            <label class="form-label" for="theme-option-widget-ribbon-color">
                              <span class="help">
                                {t}Section header icon color{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-widget-header-ribbon-color" name="theme-option-widget-header-ribbon-color" ng-model="settings.theme_options.widget_header_ribbon_color">
                                  <option value="[% widget_header_ribbon_color_name %]" ng-repeat="(widget_header_ribbon_color_name,widget_header_ribbon_color_value) in extra.theme_skins[settings.theme_skin].params.options.widget_header_ribbon_color.options" ng-selected="[% widget_header_ribbon_color_name === settings.theme_options.widget_header_ribbon_color %]">[% widget_header_ribbon_color_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 m-b-15">
                        <h4>
                          <i class="fa fa-code-fork"></i>
                          {t}Breadcrumb{/t}
                        </h4>
                        <label class="form-label" for="theme-header-color">
                          <span class="help">
                            {t}Set display of routes{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-breadcrumb" name="theme-breadcrumb" ng-model="settings.theme_options.breadcrumb" required>
                              <option value="[% breadcrumb_name %]" ng-repeat="(breadcrumb_name,breadcrumb_value) in extra.theme_skins[settings.theme_skin].params.options.breadcrumb.options" ng-selected="[% breadcrumb_name == settings.theme_options.breadcrumb || settings.theme_options.breadcrumb == undefined %]">[% breadcrumb_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                      <div class="col-xs-12 col-md-5 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-content-imageratio-normal">
                          <h4>
                            <i class="fa fa-image"></i>
                            {t}Normal contents images{/t}
                          </h4>
                          <span class="help">
                            {t}Choose image aspect ratio{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.content_imageratio_normal">
                            <div class="panel panel-default col-xs-4" ng-repeat="(content_imageratio_normal_name,content_imageratio_normal_value) in extra.theme_skins[settings.theme_skin].params.options.content_imageratio_normal.options">
                              <div class="radio">
                                <input id="theme-option-content-imageratio-normal-[% content_imageratio_normal_name %]" name="theme-option-content-imageratio-normal" ng-model="settings.theme_options.content_imageratio_normal" value="[% content_imageratio_normal_name %]" ng-checked="[% content_imageratio_normal_name == settings.theme_options.content_imageratio_normal %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-content-imageratio-normal-[% content_imageratio_normal_name %]">
                                  <img src="/themes/apolo/images/admin/imageratio_normal-[% content_imageratio_normal_name %].jpg" alt="[% content_imageratio_normal_value %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% content_imageratio_normal_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-5 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-content-imageratio-list">
                          <h4>
                            <i class="fa fa-image"></i>
                            {t}List contents images{/t}
                          </h4>
                          <span class="help">
                            {t}Choose image aspect ratio{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.content_imageratio_list">
                            <div class="panel panel-default col-xs-4" ng-repeat="(content_imageratio_list_name,content_imageratio_list_value) in extra.theme_skins[settings.theme_skin].params.options.content_imageratio_list.options">
                              <div class="radio">
                                <input id="theme-option-content-imageratio-list-[% content_imageratio_list_name %]" name="theme-option-content-imageratio-list" ng-model="settings.theme_options.content_imageratio_list" value="[% content_imageratio_list_name %]" ng-checked="[% content_imageratio_list_name === settings.theme_options.content_imageratio_list %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-content-imageratio-list-[% content_imageratio_list_name %]">
                                  <img src="/themes/apolo/images/admin/imageratio_list-[% content_imageratio_list_name %].jpg" alt="[% content_imageratio_list_value %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% content_imageratio_list_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-5 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-content-imageratio-tiny">
                          <h4>
                            <i class="fa fa-image"></i>
                            {t}Tiny contents images{/t}
                          </h4>
                          <span class="help">
                            {t}Choose image aspect ratio{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.content_imageratio_tiny">
                            <div class="panel panel-default col-xs-4" ng-repeat="(content_imageratio_tiny_name,content_imageratio_tiny_value) in extra.theme_skins[settings.theme_skin].params.options.content_imageratio_tiny.options">
                              <div class="radio">
                                <input id="theme-option-content-imageratio-tiny-[% content_imageratio_tiny_name %]" name="theme-option-content-imageratio-tiny" ng-model="settings.theme_options.content_imageratio_tiny" value="[% content_imageratio_tiny_name %]" ng-checked="[% content_imageratio_tiny_name === settings.theme_options.content_imageratio_tiny %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-content-imageratio-tiny-[% content_imageratio_tiny_name %]">
                                  <img src="/themes/apolo/images/admin/imageratio_tiny-[% content_imageratio_tiny_name %].jpg" alt="[% content_imageratio_tiny_value %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% content_imageratio_tiny_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-code"></i>
                      Custom {t}CSS{/t}
                    </h4>
                    <div class="form-group col-md-6">
                      <label class="form-label" for="custom-theme-css">
                        <span class="help">{t}This css will be added at the end of the original{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="custom-theme-css" name="theme-option-custom-css" ng-model="settings.theme_options.custom_css" rows="15"></textarea>
                      </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane fade" id="tabFonts">
                    <div ng-if="extra.theme_skins[settings.theme_skin].params.fonts">
                      <div class="row">
                        <div class="col-xs-12 m-b-15">
                          <h4>
                            <i class="fa fa-font"></i>
                            {t}Theme fonts{/t}
                          </h4>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label" for="theme-font">
                            <span class="help">
                              {t}Your theme offers multiple fonts to match your page style. Select yout main font (titles, headings…).{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-font" name="theme-font" ng-model="settings.theme_font" required>
                                <option value="[% font_name %]" ng-repeat="(font_name,font_url) in extra.theme_skins[settings.theme_skin].params.fonts" ng-selected="[% font_name === settings.theme_font %]">[% font_url %]</option>
                              </select>
                            </div>
                          </div>
                          <label class="form-label m-t-15" for="theme-main-font-size">
                            <span class="help">
                              {t}Main font base size{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-main-font-size" name="theme-main-font-size" ng-model="settings.theme_options.main_font_size" required>
                                <option value="[% main_font_size_name %]" ng-repeat="(main_font_size_name,main_font_size_value) in extra.theme_skins[settings.theme_skin].params.options.main_font_size.options" ng-selected="[% main_font_size_name === settings.theme_options.main_font_size %]">[% main_font_size_value %]</option>
                              </select>
                            </div>
                          </div>
                          <label class="form-label m-t-15" for="theme-main-font-weight">
                            <span class="help">
                              {t}Main font weight{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-main-font-weight" name="theme-main-font-weight" ng-model="settings.theme_options.main_font_weight" required>
                                <option value="[% main_font_weight_name %]" ng-repeat="(main_font_weight_name,main_font_weight_value) in extra.theme_skins[settings.theme_skin].params.options.main_font_weight.options" ng-selected="[% main_font_weight_name === settings.theme_options.main_font_weight %]">[% main_font_weight_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label" for="theme-font-secondary">
                            <span class="help">
                              {t}Secondary font (body, summary…){/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-font-secondary" name="theme-font-secondary" ng-model="settings.theme_font_secondary" required>
                                <option value="[% secondary_font_name %]" ng-repeat="(secondary_font_name,secondary_font_url) in extra.theme_skins[settings.theme_skin].params.fonts" ng-selected="[% secondary_font_name === settings.theme_font_secondary || settings.theme_font_secondary == undefined %]">[% secondary_font_url %]</option>
                              </select>
                            </div>
                          </div>
                          <label class="form-label m-t-15" for="theme-second-font-size">
                            <span class="help">
                              {t}Secondary font base size{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-second-font-size" name="theme-second-font-size" ng-model="settings.theme_options.second_font_size" required>
                                <option value="[% second_font_size_name %]" ng-repeat="(second_font_size_name,second_font_size_value) in extra.theme_skins[settings.theme_skin].params.options.second_font_size.options" ng-selected="[% second_font_size_name === settings.theme_options.second_font_size || settings.theme_options.second_font_size == undefined %]">[% second_font_size_value %]</option>
                              </select>
                            </div>
                          </div>
                          <label class="form-label m-t-15" for="theme-second-font-weight">
                            <span class="help">
                              {t}Secondary font weight{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-second-font-weight" name="theme-second-font-weight" ng-model="settings.theme_options.second_font_weight" required>
                                <option value="[% second_font_weight_name %]" ng-repeat="(second_font_weight_name,second_font_weight_value) in extra.theme_skins[settings.theme_skin].params.options.second_font_weight.options" ng-selected="[% second_font_weight_name === settings.theme_options.second_font_weight || settings.theme_options.second_font_weight == undefined %]">[% second_font_weight_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane fade" id="tabHeader">
                    <div class="row" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-align-center"></i>
                          {t}Main header alignment{/t}
                        </h4>
                        <label class="form-label" for="theme-header-align">
                          <span class="help">
                            {t}Set main logo position{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-header-align" name="theme-header-align" ng-model="settings.theme_options.header_align" required>
                              <option value="[% header_align_name %]" ng-repeat="(header_align_name,header_align_value) in extra.theme_skins[settings.theme_skin].params.options.header_align.options" ng-selected="[% header_align_name === settings.theme_options.header_align || settings.theme_options.header_align == undefined %]">[% header_align_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-paint-brush"></i>
                          {t}Main header background{/t}
                        </h4>
                        <label class="form-label" for="theme-header-color">
                          <span class="help">
                            {t}Choose header appearance{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-header-color" name="theme-header-color" ng-model="settings.theme_options.header_color" required>
                              <option value="[% header_color_name %]" ng-repeat="(header_color_name,header_color_value) in extra.theme_skins[settings.theme_skin].params.options.header_color.options" ng-selected="[% header_color_name === settings.theme_options.header_color || settings.theme_options.header_color == undefined %]">[% header_color_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.theme_options.header_color == 'default'">
                        <h4>
                          <i class="fa fa-eyedropper"></i>
                          {t}Main header border color{/t}
                        </h4>
                        <label class="form-label" for="theme-header-border-color">
                          <span class="help">
                            {t}Choose header border bottom color{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-addon" ng-style="{ 'background-color': settings.theme_options.header_border_color }">
                              &nbsp;&nbsp;&nbsp;&nbsp;
                            </span>
                            <input class="form-control" colorpicker="hex" id="header-border-color" name="header-border-color" ng-model="settings.theme_options.header_border_color" type="text">
                            <div class="input-group-btn">
                              <button class="btn btn-default" ng-click="settings.theme_options.header_border_color = backup.theme_options.header_border_color" type="button">{t}Reset{/t}</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane fade" id="tabMenu">
                    <div ng-if="extra.theme_skins[settings.theme_skin].params.options">
                      <div class="row">
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <h4>
                            <i class="fa fa-paint-brush"></i>
                            {t}Main menu background{/t}
                          </h4>
                          <label class="form-label" for="theme-menu-color">
                            <span class="help">
                              {t}Choose menu appearance{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-menu-color" name="theme-menu-color" ng-model="settings.theme_options.menu_color" required>
                                <option value="[% menu_color_name %]" ng-repeat="(menu_color_name,menu_color_value) in extra.theme_skins[settings.theme_skin].params.options.menu_color.options" ng-selected="[% menu_color_name === settings.theme_options.menu_color || settings.theme_options.menu_color == undefined %]">[% menu_color_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <h4>
                            <i class="fa fa-underline"></i>
                            {t}Main menu border width{/t}
                          </h4>
                          <label class="form-label" for="theme-menu-border">
                            <span class="help">
                              {t}Choose main menu bottom border width{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-menu-border" name="theme-menu-border" ng-model="settings.theme_options.menu_border" required>
                                <option value="[% menu_border_name %]" ng-repeat="(menu_border_name,menu_border_value) in extra.theme_skins[settings.theme_skin].params.options.menu_border.options" ng-selected="[% menu_border_name === settings.theme_options.menu_border || settings.theme_options.menu_border == undefined %]">[% menu_border_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.theme_options.menu_color == 'default'">
                          <h4>
                            <i class="fa fa-eyedropper"></i>
                            {t}Main menu border color{/t}
                          </h4>
                          <label class="form-label" for="theme-menu-border-color">
                            <span class="help">
                              {t}Choose main menu border bottom color{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <span class="input-group-addon" ng-style="{ 'background-color': settings.theme_options.menu_border_color }">
                                &nbsp;&nbsp;&nbsp;&nbsp;
                              </span>
                              <input class="form-control" colorpicker="hex" id="menu-border-color" name="menu-border-color" ng-model="settings.theme_options.menu_border_color" type="text">
                              <div class="input-group-btn">
                                <button class="btn btn-default" ng-click="settings.theme_options.menu_border_color = backup.theme_options.menu_border_color" type="button">{t}Reset{/t}</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <h4>
                            <i class="fa fa-eyedropper"></i>
                            {t}Main menu link color{/t}
                          </h4>
                          <label class="form-label" for="theme-menu-link-color">
                            <span class="help">
                              {t}Choose main menu links color{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-menu-link-color" name="theme-menu-link-color" ng-model="settings.theme_options.menu_link_color" required>
                                <option value="[% menu_link_color_name %]" ng-repeat="(menu_link_color_name,menu_link_color_value) in extra.theme_skins[settings.theme_skin].params.options.menu_link_color.options" ng-selected="[% menu_link_color_name === settings.theme_options.menu_link_color || settings.theme_options.menu_link_color == undefined %]">[% menu_link_color_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane fade" id="tabFrontpage">
                    <div ng-if="extra.theme_skins[settings.theme_skin].params.options">

                      <div class="row">
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-content-category-name">
                            <h4>
                              <i class="fa fa-folder"></i>
                              {t}Category name{/t} / {t}Pretitle{/t}
                            </h4>
                            <span class="help">
                              {t}Display pretitle or category in contents{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-option-content-category-name" name="theme-option-content-category-name" ng-model="settings.theme_options.content_category_name">
                                <option value="[% content_category_key %]" ng-repeat="(content_category_key,content_category_value) in extra.theme_skins[settings.theme_skin].params.options.content_category_name.options" ng-selected="[% content_category_key === settings.theme_options.content_category_name %]">[% content_category_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-content-subtitle">
                            <h4>
                              <i class="fa fa-header"></i>
                              {t}Subtitle{/t}
                            </h4>
                            <span class="help">
                              {t}Display subtitle{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-option-content-subtitle" name="theme-option-content-subtitle" ng-model="settings.theme_options.content_subtitle">
                                <option value="[% content_subtitle_name %]" ng-repeat="(content_subtitle_name,content_subtitle_value) in extra.theme_skins[settings.theme_skin].params.options.content_subtitle.options" ng-selected="[% content_subtitle_name === settings.theme_options.content_subtitle || settings.theme_options.content_subtitle == undefined %]">[% content_subtitle_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-content-summary">
                            <h4>
                              <i class="fa fa-align-left"></i>
                              {t}Summary{/t}
                            </h4>
                            <span class="help">
                              {t}Display summary/description{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-option-content-summary" name="theme-option-content-summary" ng-model="settings.theme_options.content_summary">
                                <option value="[% content_summary_name %]" ng-repeat="(content_summary_name,content_summary_value) in extra.theme_skins[settings.theme_skin].params.options.content_summary.options" ng-selected="[% content_summary_name === settings.theme_options.content_summary || settings.theme_options.content_summary == undefined %]">[% content_summary_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-content-info">
                            <h4>
                              <i class="fa fa-info"></i>
                              {t}Info{/t}
                            </h4>
                          </label>
                        </div>
                        <div class="col-xs-12">
                          <div class="row">
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-content-author">
                                <span class="help">
                                  {t}Display content's author{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-content-author" name="theme-option-content-author" ng-model="settings.theme_options.content_author">
                                    <option value="[% content_author_name %]" ng-repeat="(content_author_name,content_author_value) in extra.theme_skins[settings.theme_skin].params.options.content_author.options" ng-selected="[% content_author_name === settings.theme_options.content_author || settings.theme_options.content_author == undefined %]">[% content_author_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.theme_options.content_author === 'true'">
                              <label class="form-label m-b-15" for="theme-option-content-author-photo">
                                <span class="help">
                                  {t}Display author's photo{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-content-author-photo" name="theme-option-content-author-photo" ng-model="settings.theme_options.content_author_photo">
                                    <option value="[% content_author_photo_name %]" ng-repeat="(content_author_photo_name,content_author_photo_value) in extra.theme_skins[settings.theme_skin].params.options.content_author_photo.options" ng-selected="[% content_author_photo_name === settings.theme_options.content_author_photo || settings.theme_options.content_author_photo == undefined %]">[% content_author_photo_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-content-date">
                                <span class="help">
                                  {t}Display content's date{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-content-date" name="theme-option-content-date" ng-model="settings.theme_options.content_date">
                                    <option value="[% content_date_name %]" ng-repeat="(content_date_name,content_date_value) in extra.theme_skins[settings.theme_skin].params.options.content_date.options" ng-selected="[% content_date_name === settings.theme_options.content_date || settings.theme_options.content_date == undefined %]">[% content_date_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.theme_options.content_date === 'true'">
                              <label class="form-label m-b-15" for="theme-option-content-time">
                                <span class="help">
                                  {t}Display content's time{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-content-time" name="theme-option-content-time" ng-model="settings.theme_options.content_time">
                                    <option value="[% content_time_name %]" ng-repeat="(content_time_name,content_time_value) in extra.theme_skins[settings.theme_skin].params.options.content_time.options" ng-selected="[% content_time_name === settings.theme_options.content_time || settings.theme_options.content_time == undefined %]">[% content_time_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-content-readtime">
                                <span class="help">
                                  {t}Display content's readtime{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-content-readtime" name="theme-option-content-readtime" ng-model="settings.theme_options.content_readtime">
                                    <option value="[% content_readtime_name %]" ng-repeat="(content_readtime_name,content_readtime_value) in extra.theme_skins[settings.theme_skin].params.options.content_readtime.options" ng-selected="[% content_readtime_name === settings.theme_options.content_readtime || settings.theme_options.content_readtime == undefined %]">[% content_readtime_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane fade" id="tabArchive">
                    <div ng-if="extra.theme_skins[settings.theme_skin].params.options">
                      <div class="row">
                        <div class="col-xs-12 col-md-5 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-appearance">
                            <h4>
                              <i class="fa fa-navicon"></i>
                              {t}Layout{/t}
                            </h4>
                            <span class="help">
                              {t}Choose archive list appearance{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="row" ng-model="settings.theme_options.archive_appearance">
                              <div class="panel panel-default col-xs-5" ng-repeat="(archive_appearance_name,archive_appearance_value) in extra.theme_skins[settings.theme_skin].params.options.archive_appearance.options">
                                <div class="radio">
                                  <input id="theme-option-archive-appearance-[% archive_appearance_name %]" name="theme-option-archive-appearance" ng-model="settings.theme_options.archive_appearance" value="[% archive_appearance_name %]" ng-checked="[% archive_appearance_name === settings.theme_options.archive_appearance %]" type="radio"/>
                                  <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-archive-appearance-[% archive_appearance_name %]">
                                    <img src="/themes/apolo/images/admin/archive_appearance-[% archive_appearance_name %].jpg" alt="[% archive_appearance_value %]" class="img img-responsive img-rounded m-b-10">
                                    <h5>[% archive_appearance_value %]</h5>
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-5 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-cover">
                            <h4>
                              <i class="fa fa-navicon"></i>
                              {t}Category cover{/t}
                            </h4>
                            <span class="help">
                              {t}Show category cover (if exists){/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="row" ng-model="settings.theme_options.archive_cover">
                              <div class="panel panel-default col-xs-5" ng-repeat="(archive_cover_name,archive_cover_value) in extra.theme_skins[settings.theme_skin].params.options.archive_cover.options">
                                <div class="radio">
                                  <input id="theme-option-archive-cover-[% archive_cover_name %]" name="theme-option-archive-cover" ng-model="settings.theme_options.archive_cover" value="[% archive_cover_name %]" ng-checked="[% archive_cover_name === settings.theme_options.archive_cover %]" type="radio"/>
                                  <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-archive-cover-[% archive_cover_name %]">
                                    <img src="/themes/apolo/images/admin/archive_cover-[% archive_cover_name %].jpg" alt="[% archive_cover_value %]" class="img img-responsive img-rounded m-b-10">
                                    <h5>[% archive_cover_value %]</h5>
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-category-name">
                            <h4>
                              <i class="fa fa-folder"></i>
                              {t}Category name{/t} / {t}Pretitle{/t}
                            </h4>
                            <span class="help">
                              {t}Display pretitle or category in contents{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-option-archive-category-name" name="theme-option-archive-category-name" ng-model="settings.theme_options.archive_category_name">
                                <option value="[% archive_category_key %]" ng-repeat="(archive_category_key,archive_category_value) in extra.theme_skins[settings.theme_skin].params.options.archive_category_name.options" ng-selected="[% archive_category_key === settings.theme_options.archive_category_name %]">[% archive_category_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-subtitle">
                            <h4>
                              <i class="fa fa-header"></i>
                              {t}Subtitle{/t}
                            </h4>
                            <span class="help">
                              {t}Display subtitle{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-option-archive-subtitle" name="theme-option-archive-subtitle" ng-model="settings.theme_options.archive_subtitle">
                                <option value="[% archive_subtitle_name %]" ng-repeat="(archive_subtitle_name,archive_subtitle_value) in extra.theme_skins[settings.theme_skin].params.options.archive_subtitle.options" ng-selected="[% archive_subtitle_name === settings.theme_options.archive_subtitle || settings.theme_options.archive_subtitle == undefined %]">[% archive_subtitle_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-summary">
                            <h4>
                              <i class="fa fa-align-left"></i>
                              {t}Summary{/t}
                            </h4>
                            <span class="help">
                              {t}Display summary/description{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-option-archive-summary" name="theme-option-archive-summary" ng-model="settings.theme_options.archive_summary">
                                <option value="[% archive_summary_name %]" ng-repeat="(archive_summary_name,archive_summary_value) in extra.theme_skins[settings.theme_skin].params.options.archive_summary.options" ng-selected="[% archive_summary_name === settings.theme_options.archive_summary || settings.theme_options.archive_summary == undefined %]">[% archive_summary_value %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15">
                            <h4>
                              <i class="fa fa-info"></i>
                              {t}Info{/t}
                            </h4>
                          </label>
                        </div>
                        <div class="col-xs-12">
                          <div class="row">
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-archive-author">
                                <span class="help">
                                  {t}Display content's author{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-archive-author" name="theme-option-archive-author" ng-model="settings.theme_options.archive_author">
                                    <option value="[% archive_author_name %]" ng-repeat="(archive_author_name,archive_author_value) in extra.theme_skins[settings.theme_skin].params.options.archive_author.options" ng-selected="[% archive_author_name === settings.theme_options.archive_author || settings.theme_options.archive_author == undefined %]">[% archive_author_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.theme_options.archive_author === 'true'">
                              <label class="form-label m-b-15" for="theme-option-archive-author-photo">
                                <span class="help">
                                  {t}Display author's photo{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-archive-author-photo" name="theme-option-archive-author-photo" ng-model="settings.theme_options.archive_author_photo">
                                    <option value="[% archive_author_photo_name %]" ng-repeat="(archive_author_photo_name,archive_author_photo_value) in extra.theme_skins[settings.theme_skin].params.options.archive_author_photo.options" ng-selected="[% archive_author_photo_name === settings.theme_options.archive_author_photo || settings.theme_options.archive_author_photo == undefined %]">[% archive_author_photo_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-archive-date">
                                <span class="help">
                                  {t}Display content's date{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-archive-date" name="theme-option-archive-date" ng-model="settings.theme_options.archive_date">
                                    <option value="[% archive_date_name %]" ng-repeat="(archive_date_name,archive_date_value) in extra.theme_skins[settings.theme_skin].params.options.archive_date.options" ng-selected="[% archive_date_name === settings.theme_options.archive_date || settings.theme_options.archive_date == undefined %]">[% archive_date_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.theme_options.archive_date === 'true'">
                              <label class="form-label m-b-15" for="theme-option-archive-time">
                                <span class="help">
                                  {t}Display content's time{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-archive-time" name="theme-option-archive-time" ng-model="settings.theme_options.archive_time">
                                    <option value="[% archive_time_name %]" ng-repeat="(archive_time_name,archive_time_value) in extra.theme_skins[settings.theme_skin].params.options.archive_time.options" ng-selected="[% archive_time_name === settings.theme_options.archive_time || settings.theme_options.archive_time == undefined %]">[% archive_time_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-archive-readtime">
                                <span class="help">
                                  {t}Display content's readtime{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-option-archive-readtime" name="theme-option-archive-readtime" ng-model="settings.theme_options.archive_readtime">
                                    <option value="[% archive_readtime_name %]" ng-repeat="(archive_readtime_name,archive_readtime_value) in extra.theme_skins[settings.theme_skin].params.options.archive_readtime.options" ng-selected="[% archive_readtime_name === settings.theme_options.archive_readtime || settings.theme_options.archive_readtime == undefined %]">[% archive_readtime_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div role="tabpanel" class="tab-pane fade" id="tabInners">
                    <div class="row" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                      <div class="col-xs-12 col-md-8">
                        <div class="row">
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-article-header">
                              <h4>
                                <i class="fa fa-align-center"></i>
                                {t}Content header{/t}
                              </h4>
                              <span class="help">
                                {t}Display inner contents header at full width or inbody column{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="row" ng-model="settings.theme_options.article_header">
                                <div class="panel panel-default col-xs-5 col-md-4" ng-repeat="(article_header_name,article_header_value) in extra.theme_skins[settings.theme_skin].params.options.article_header.options">
                                  <div class="radio">
                                    <input id="theme-option-article-header-[% article_header_name %]" name="theme-option-article-header" ng-model="settings.theme_options.article_header" value="[% article_header_name %]" ng-checked="[% article_header_name === settings.theme_options.article_header %]" type="radio"/>
                                    <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-article-header-[% article_header_name %]">
                                      <img src="/themes/apolo/images/admin/article_header-[% article_header_name %].jpg" alt="[% article_header_name %]" class="img img-responsive img-rounded m-b-10">
                                      <h5>[% article_header_value %]</h5>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-article-layout">
                              <h4>
                                <i class="fa fa-columns"></i>
                                {t}Sidebar{/t}
                              </h4>
                              <span class="help">
                                {t}Show or hide right sidebar{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="row" ng-model="settings.theme_options.article_layout">
                                <div class="panel panel-default col-xs-5 col-md-4" ng-repeat="(article_layout_name,article_layout_value) in extra.theme_skins[settings.theme_skin].params.options.article_layout.options">
                                  <div class="radio">
                                    <input id="theme-option-article-layout-[% article_layout_name %]" name="theme-option-article-layout" ng-model="settings.theme_options.article_layout" value="[% article_layout_name %]" ng-checked="[% article_layout_name === settings.theme_options.article_layout %]" type="radio"/>
                                    <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-article-layout-[% article_layout_name %]">
                                      <img src="/themes/apolo/images/admin/article_layout-[% article_layout_name %].jpg" alt="[% article_layout_value %]" class="img img-responsive img-rounded m-b-10">
                                      <h5>[% article_layout_value %]</h5>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-media-header">
                              <h4>
                                <i class="fa fa-picture-o"></i>
                                {t}Featured media{/t}
                              </h4>
                              <span class="help">
                                {t}Display in article header or just before the body{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="row" ng-model="settings.theme_options.article_header_media">
                                <div class="panel panel-default col-xs-5 col-md-4" ng-repeat="(header_media_name,header_media_value) in extra.theme_skins[settings.theme_skin].params.options.article_header_media.options">
                                  <div class="radio">
                                    <input id="theme-option-media-header-[% header_media_name %]" name="theme-option-media-header" ng-model="settings.theme_options.article_header_media" value="[% header_media_name %]" ng-checked="[% header_media_name === settings.theme_options.article_header_media %]" type="radio"/>
                                    <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-media-header-[% header_media_name %]">
                                      <img src="/themes/apolo/images/admin/article_header_media-[% header_media_name %].jpg" alt="[% header_media_name %]" class="img img-responsive img-rounded m-b-10">
                                      <h5>[% header_media_value %]</h5>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15" ng-if="settings.theme_options.article_header_media === 'header'">
                            <label class="form-label m-b-15" for="theme-option-order-header">
                              <h4>
                                <i class="fa fa-sort-amount-asc"></i>
                                {t}Content header display order{/t}
                              </h4>
                              <span class="help">
                                {t}Choose what elements go first{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="row" ng-model="settings.theme_options.article_header_order">
                                <div class="panel panel-default col-xs-5 col-md-4" ng-repeat="(header_order_name,header_order_value) in extra.theme_skins[settings.theme_skin].params.options.article_header_order.options">
                                  <div class="radio">
                                    <input id="theme-option-order-header-[% header_order_name %]" name="theme-option-order-header" ng-model="settings.theme_options.article_header_order" value="[% header_order_name %]" ng-checked="[% header_order_name === settings.theme_options.article_header_order %]" type="radio"/>
                                    <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-order-header-[% header_order_name %]">
                                      <img src="/themes/apolo/images/admin/article_header_order-[% header_order_name %].jpg" alt="[% header_order_name %]" class="img img-responsive img-rounded m-b-10">
                                      <h5>[% header_order_value %]</h5>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15" ng-if="settings.theme_options.article_header_media === 'header'">
                            <label class="form-label m-b-15" for="theme-option-align-header">
                              <h4>
                                <i class="fa fa-align-left"></i>
                                {t}Content header align{/t}
                              </h4>
                              <span class="help">
                                {t}Choose headings alignment{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="row" ng-model="settings.theme_options.article_header_align">
                                <div class="panel panel-default col-xs-5 col-md-4" ng-repeat="(header_align_name,header_align_value) in extra.theme_skins[settings.theme_skin].params.options.article_header_align.options">
                                  <div class="radio">
                                    <input id="theme-option-align-header-[% header_align_name %]" name="theme-option-align-header" ng-model="settings.theme_options.article_header_align" value="[% header_align_name %]" ng-checked="[% header_align_name === settings.theme_options.article_header_align %]" type="radio"/>
                                    <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-align-header-[% header_align_name %]">
                                      <img src="/themes/apolo/images/admin/article_header_align-[% header_align_name %].jpg" alt="[% header_align_value %]" class="img img-responsive img-rounded m-b-10">
                                      <h5>[% header_align_value %]</h5>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4">
                        <div class="row">

                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-share-tools">
                              <h4>
                                <i class="fa fa-share-alt-square"></i>
                                {t}Share tools{/t}
                              </h4>
                              <span class="help">
                                {t}Choose where to display{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-share-tools" name="theme-option-share-tools" ng-model="settings.theme_options.share_tools">
                                  <option value="[% share_tools_name %]" ng-repeat="(share_tools_name,share_tools_value) in extra.theme_skins[settings.theme_skin].params.options.share_tools.options" ng-selected="[% share_tools_name === settings.theme_options.share_tools || settings.theme_options.share_tools == undefined %]">[% share_tools_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-tags-display">
                              <h4>
                                <i class="fa fa-tag"></i>
                                {t}Tags{/t}
                              </h4>
                              <span class="help">
                                {t}Choose where to display{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-tags-display" name="theme-option-tags-display" ng-model="settings.theme_options.tags_display">
                                  <option value="[% tags_display_name %]" ng-repeat="(tags_display_name,tags_display_value) in extra.theme_skins[settings.theme_skin].params.options.tags_display.options" ng-selected="[% tags_display_name === settings.theme_options.tags_display || settings.theme_options.tags_display == undefined %]">[% tags_display_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-related-contents">
                              <h4>
                                <i class="fa fa-bars"></i>
                                {t}Related contents{/t}
                              </h4>
                              <span class="help">
                                {t}Choose where to display{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-related-contents" name="theme-option-related-contents" ng-model="settings.theme_options.related_contents">
                                  <option value="[% related_contents_name %]" ng-repeat="(related_contents_name,related_contents_value) in extra.theme_skins[settings.theme_skin].params.options.related_contents.options" ng-selected="[% related_contents_name === settings.theme_options.related_contents || settings.theme_options.related_contents == undefined %]">[% related_contents_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-related-contents-auto">
                              <h4>
                                <i class="fa fa-bars"></i>
                                {t}Automatic related contents{/t}
                              </h4>
                              <span class="help">
                                {t}Display suggested related when not manual ones selected{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-related-contents-auto" name="theme-option-related-contents-auto" ng-model="settings.theme_options.related_contents_auto">
                                  <option value="[% related_contents_auto_name %]" ng-repeat="(related_contents_auto_name,related_contents_auto_value) in extra.theme_skins[settings.theme_skin].params.options.related_contents_auto.options" ng-selected="[% related_contents_auto_name === settings.theme_options.related_contents_auto || settings.theme_options.related_contents_auto == undefined %]">[% related_contents_auto_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15" ng-if="settings.theme_options.related_contents_auto == 'true'">
                            <label class="form-label m-b-15" for="theme-option-related-contents-auto-position">
                              <span class="help">
                                {t}Choose where to display{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-related-contents-auto-position" name="theme-option-related-contents-auto-position" ng-model="settings.theme_options.related_contents_auto_position">
                                  <option value="[% related_contents_auto_position_name %]" ng-repeat="(related_contents_auto_position_name,related_contents_auto_position_value) in extra.theme_skins[settings.theme_skin].params.options.related_contents_auto_position.options" ng-selected="[% related_contents_auto_position_name === settings.theme_options.related_contents_auto_position || settings.theme_options.related_contents_auto_position == undefined %]">[% related_contents_auto_position_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <label class="form-label m-b-15" for="theme-option-inner-content-info">
                            <h4>
                              <i class="fa fa-info"></i>
                              {t}Info{/t}
                            </h4>
                          </label>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-inner-content-author">
                              <span class="help">
                                {t}Display content's author{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-inner-content-author" name="theme-option-inner-content-author" ng-model="settings.theme_options.inner_content_author">
                                  <option value="[% content_author_name %]" ng-repeat="(content_author_name,content_author_value) in extra.theme_skins[settings.theme_skin].params.options.inner_content_author.options" ng-selected="[% content_author_name === settings.theme_options.inner_content_author %]">[% content_author_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15" ng-if="settings.theme_options.inner_content_author === 'true'">
                            <label class="form-label m-b-15" for="theme-option-inner-content-author-photo">
                              <span class="help">
                                {t}Display author's photo{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-inner-content-author-photo" name="theme-option-inner-content-author-photo" ng-model="settings.theme_options.inner_content_author_photo">
                                  <option value="[% content_author_photo_name %]" ng-repeat="(content_author_photo_name,content_author_photo_value) in extra.theme_skins[settings.theme_skin].params.options.inner_content_author_photo.options" ng-selected="[% content_author_photo_name === settings.theme_options.inner_content_author_photo %]">[% content_author_photo_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-inner-content-date">
                              <span class="help">
                                {t}Display content's date{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-inner-content-date" name="theme-option-inner-content-date" ng-model="settings.theme_options.inner_content_date">
                                  <option value="[% content_date_name %]" ng-repeat="(content_date_name,content_date_value) in extra.theme_skins[settings.theme_skin].params.options.inner_content_date.options" ng-selected="[% content_date_name === settings.theme_options.inner_content_date %]">[% content_date_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15" ng-if="settings.theme_options.inner_content_date === 'true'">
                            <label class="form-label m-b-15" for="theme-option-inner-content-time">
                              <span class="help">
                                {t}Display content's time{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-inner-content-time" name="theme-option-inner-content-time" ng-model="settings.theme_options.inner_content_time">
                                  <option value="[% content_time_name %]" ng-repeat="(content_time_name,content_time_value) in extra.theme_skins[settings.theme_skin].params.options.inner_content_time.options" ng-selected="[% content_time_name === settings.theme_options.inner_content_time %]">[% content_time_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-inner-content-readtime">
                              <span class="help">
                                {t}Display content's readtime{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-inner-content-readtime" name="theme-option-inner-content-readtime" ng-model="settings.theme_options.inner_content_readtime">
                                  <option value="[% content_readtime_name %]" ng-repeat="(content_readtime_name,content_readtime_value) in extra.theme_skins[settings.theme_skin].params.options.inner_content_readtime.options" ng-selected="[% content_readtime_name === settings.theme_options.inner_content_readtime %]">[% content_readtime_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-sidebar-widgets">
                              <h4>
                                <i class="fa fa-puzzle-piece"></i>
                                {t}Sidebar widgets{/t}
                              </h4>
                              <span class="help">
                                {t}Choose widgets to display{/t}
                              </span>
                            </label>
                            <div class="form-group m-b-0">
                              <div class="checkbox p-b-10">
                                <input id="theme-option-sidebar-widget-today-news" name="theme-option-sidebar-widget-today-news" ng-model="settings.theme_options.sidebar_widget_today_news" ng-checked="[% settings.theme_options.sidebar_widget_today_news != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-sidebar-widget-today-news">
                                  <span>{t domain="base"}Today news{/t}</span>
                                </label>
                              </div>
                              <div class="checkbox p-b-10">
                                <input id="theme-option-sidebar-widget-most-viewed" name="theme-option-sidebar-widget-most-viewed" ng-model="settings.theme_options.sidebar_widget_most_viewed" ng-checked="[% settings.theme_options.sidebar_widget_most_viewed != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-sidebar-widget-most-viewed">
                                  <span>{t domain="base"}Most viewed{/t}</span>
                                </label>
                              </div>
                              <div class="checkbox p-b-10">
                                <input id="theme-option-sidebar-widget-most-seeing" name="theme-option-sidebar-widget-most-seeing" ng-model="settings.theme_options.sidebar_widget_most_seeing_recent" ng-checked="[% settings.theme_options.sidebar_widget_most_seeing_recent %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-sidebar-widget-most-seeing">
                                  <span>{t domain="base"}Most seeing/recent{/t}</span>
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-bodyend-widgets">
                              <h4>
                                <i class="fa fa-puzzle-piece"></i>
                                {t}Body end widgets{/t}
                              </h4>
                              <span class="help">
                                {t}Choose widgets to display{/t}
                              </span>
                            </label>
                            <div class="form-group m-b-0">
                              <div class="checkbox p-b-10">
                                <input id="theme-option-bodyend-widget-more-in-section" name="theme-option-bodyend-widget-more-in-section" ng-model="settings.theme_options.widget_more_in_section" ng-checked="[% settings.theme_options.widget_more_in_section %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-bodyend-widget-more-in-section">
                                  <span>{t domain="base"}More in section{/t}</span>
                                </label>
                              </div>
                              <div class="checkbox p-b-10">
                                <input id="theme-option-bodyend-widget-more-in-frontpage" name="theme-option-bodyend-widget-more-in-frontpage" ng-model="settings.theme_options.widget_more_in_frontpage" ng-checked="[% settings.theme_options.widget_more_in_frontpage %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-bodyend-widget-more-in-frontpage">
                                  <span>{t domain="base"}More in frontpage{/t}</span>
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15" ng-if="settings.theme_options.widget_more_in_section === 'true'">
                            <label class="form-label m-b-15" for="theme-option-more-in-section-layout">
                              <span class="help">
                                {t}More in section widget layout{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-more-in-section-layout" name="theme-option-more-in-section-layout" ng-model="settings.theme_options.widget_more_in_section_layout">
                                  <option value="[% more_in_section_layout_name %]" ng-repeat="(more_in_section_layout_name,more_in_section_layout_value) in extra.theme_skins[settings.theme_skin].params.options.widget_more_in_section_layout.options" ng-selected="[% more_in_section_layout_name === settings.theme_options.widget_more_in_section_layout %]">[% more_in_section_layout_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15"  ng-if="settings.theme_options.widget_more_in_frontpage === 'true'">
                            <label class="form-label m-b-15" for="theme-option-more-in-frontpage-layout">
                              <span class="help">
                                {t}More in frontpage widget layout{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-more-in-frontpage-layout" name="theme-option-more-in-frontpage-layout" ng-model="settings.theme_options.widget_more_in_frontpage_layout">
                                  <option value="[% more_in_frontpage_layout_name %]" ng-repeat="(more_in_frontpage_layout_name,more_in_frontpage_layout_value) in extra.theme_skins[settings.theme_skin].params.options.widget_more_in_frontpage_layout.options" ng-selected="[% more_in_frontpage_layout_name === settings.theme_options.widget_more_in_frontpage_layout %]">[% more_in_frontpage_layout_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15" ng-if="settings.theme_options.widget_more_in_frontpage === 'true' || settings.theme_options.widget_more_in_section === 'true'">
                            <label class="form-label m-b-15" for="theme-option-suggested-items">
                              <span class="help">
                                {t}Max items on suggested widgets{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-suggested-items" name="theme-option-suggested-items" ng-model="settings.theme_options.inner_content_suggested_items">
                                  <option value="[% inner_content_suggested_items_name %]" ng-repeat="(inner_content_suggested_items_name,inner_content_suggested_items_value) in extra.theme_skins[settings.theme_skin].params.options.inner_content_suggested_items.options" ng-selected="[% inner_content_suggested_items_name === settings.theme_options.inner_content_suggested_items %]">[% inner_content_suggested_items_value %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="tabMobile">
                    <div class="row" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                      <div class="col-xs-12 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-hamburger-position">
                          <h4>
                            <i class="fa fa-align-center"></i>
                            {t}Hamburger menu{/t}
                          </h4>
                          <span class="help">
                            {t}Choose where to appear menu{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.mobile_inner_aperture">
                            <div class="panel panel-default col-xs-6 col-md-3" ng-repeat="(mobile_inner_aperture_name,mobile_inner_aperture_value) in extra.theme_skins[settings.theme_skin].params.options.mobile_inner_aperture.options">
                              <div class="radio">
                                <input id="theme-option-mobile-inner-aperture-[% mobile_inner_aperture_name %]" name="theme-option-mobile-inner-aperture" ng-model="settings.theme_options.mobile_inner_aperture" value="[% mobile_inner_aperture_name %]" ng-checked="[% mobile_inner_aperture_name === settings.theme_options.mobile_inner_aperture %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-mobile-inner-aperture-[% mobile_inner_aperture_name %]">
                                  <img src="/themes/apolo/images/admin/mobile_inner_aperture-[% mobile_inner_aperture_name %].jpg" alt="[% mobile_inner_aperture_value %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% mobile_inner_aperture_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15" ng-if="settings.logo_enabled">
                        <h4>
                          <i class="fa fa-arrows-h"></i>
                          {t}Mobile logo size{/t}
                        </h4>
                        <label class="form-label" for="theme-option-width">
                          <span class="help">
                            {t}Choose header logo default size{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-option-mobile-logo-size" name="theme-option-mobile-logo-size" ng-model="settings.theme_options.mobile_logo_size">
                              <option value="[% mobile_logo_size_name %]" ng-repeat="(mobile_logo_size_name,mobile_logo_size_value) in extra.theme_skins[settings.theme_skin].params.options.mobile_logo_size.options" ng-selected="[% mobile_logo_size_name === settings.theme_options.mobile_logo_size || settings.theme_options.mobile_logo_size == undefined %]">[% mobile_logo_size_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-mobile-top-menu">
                          <h4>
                            <i class="fa fa-bars"></i>
                            {t domain='base'}Top menu{/t}
                          </h4>
                          <span class="help">
                            {t}Display top left menu{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-option-mobile-top-menu" name="theme-option-mobile-top-menu" ng-model="settings.theme_options.mobile_top_menu">
                              <option value="[% mobile_top_menu_name %]" ng-repeat="(mobile_top_menu_name,mobile_top_menu_value) in extra.theme_skins[settings.theme_skin].params.options.mobile_top_menu.options" ng-selected="[% mobile_top_menu_name === settings.theme_options.mobile_top_menu || settings.theme_options.mobile_top_menu == undefined %]">[% mobile_top_menu_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-mobile-main-menu">
                          <h4>
                            <i class="fa fa-bars"></i>
                            {t domain='base'}Main menu{/t}
                          </h4>
                          <span class="help">
                            {t}Display main menu{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-option-mobile-main-menu" name="theme-option-mobile-main-menu" ng-model="settings.theme_options.mobile_main_menu">
                              <option value="[% mobile_main_menu_name %]" ng-repeat="(mobile_main_menu_name,mobile_main_menu_value) in extra.theme_skins[settings.theme_skin].params.options.mobile_main_menu.options" ng-selected="[% mobile_main_menu_name === settings.theme_options.mobile_main_menu || settings.theme_options.mobile_main_menu == undefined %]">[% mobile_main_menu_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                </div>
              </div>
            </div>
          {/if}
        </div>
      </div>
    </div>
  </form>

{/block}
{block name="modals"}
  <script type="text/ng-template" id="modal-import-settings">
    {include file="common/modals/_modalImportSettings.tpl"}
  </script>
  <script type="text/ng-template" id="modal-restore-settings">
    {include file="common/modals/_modalRestoreSettings.tpl"}
  </script>
{/block}
