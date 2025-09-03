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
                <a class="btn btn-white" ng-click="openRestoreModal(routing.generate('api_v1_backend_settings_theme_download'))">
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
                <ul class="nav nav-pills border-bottom border-light m-b-15 p-b-5" role="tablist">
                  <li role="presentation" class="p-l-0 active">
                    <a href="#tabGeneral" aria-controls="general" role="tab" data-toggle="tab">{t domain="base"}General{/t}</a>
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
                    <a href="#tabOpinion" aria-controls="opinion" role="tab" data-toggle="tab">{t domain="base"}Opinion{/t}</a>
                  </li>
                  <li role="presentation" ng-if="extra.theme_skins[settings.theme_skin].params.options">
                    <a href="#tabMobile" aria-controls="mobile" role="tab" data-toggle="tab">{t domain="base"}Mobile{/t}</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane fade in active" id="tabGeneral">
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
                      <div class="col-xs-12 col-md-3 m-b-15">
                        <h4>
                          <i class="fa fa-arrows-h"></i>
                          {t}Logo size{/t}
                        </h4>
                        <label class="form-label" for="theme-option-width">
                          <span class="help">
                            {t}Choose logo default size{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-option-main-logo-size" name="theme-option-main-logo-size" ng-model="settings.theme_options.main_logo_size">
                              <option value="[% main_logo_size_name %]" ng-repeat="(main_logo_size_name,main_logo_size_value) in extra.theme_skins[settings.theme_skin].params.options.main_logo_size.options" ng-selected="[% main_logo_size_name === settings.theme_options.main_logo_size || settings.theme_options.main_logo_size == undefined %]">[% main_logo_size_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-3 m-b-15">
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

                      <div ng-if="extra.theme_skins[settings.theme_skin].params.fonts">
                        <div class="col-xs-12 col-md-7 m-t-30">
                          <div class="col-xs-12">
                            <h4>
                              <i class="fa fa-font"></i>
                              {t}Theme fonts{/t}
                            </h4>
                          </div>
                          <div class="col-xs-12 col-md-6 m-b-15">
                            <div ng-if="extra.theme_skins[settings.theme_skin].params.options.main_font_size">
                              <label class="form-label m-t-15" for="theme-main-font-size">
                                <span class="help">
                                  {t}Main font base size{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-main-font-size" name="theme-main-font-size" ng-model="settings.theme_options.main_font_size">
                                    <option value="[% main_font_size_name %]" ng-repeat="(main_font_size_name,main_font_size_value) in extra.theme_skins[settings.theme_skin].params.options.main_font_size.options" ng-selected="[% main_font_size_name === settings.theme_options.main_font_size %]">[% main_font_size_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div ng-if="extra.theme_skins[settings.theme_skin].params.options.main_font_weight">
                              <label class="form-label m-t-15" for="theme-main-font-weight">
                                <span class="help">
                                  {t}Main font weight{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-main-font-weight" name="theme-main-font-weight" ng-model="settings.theme_options.main_font_weight">
                                    <option value="[% main_font_weight_name %]" ng-repeat="(main_font_weight_name,main_font_weight_value) in extra.theme_skins[settings.theme_skin].params.options.main_font_weight.options" ng-selected="[% main_font_weight_name === settings.theme_options.main_font_weight %]">[% main_font_weight_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 col-md-6 m-b-15">
                            <div ng-if="extra.theme_skins[settings.theme_skin].params.options.second_font_size">
                              <label class="form-label m-t-15" for="theme-second-font-size">
                                <span class="help">
                                  {t}Secondary font base size{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-second-font-size" name="theme-second-font-size" ng-model="settings.theme_options.second_font_size">
                                    <option value="[% second_font_size_name %]" ng-repeat="(second_font_size_name,second_font_size_value) in extra.theme_skins[settings.theme_skin].params.options.second_font_size.options" ng-selected="[% second_font_size_name === settings.theme_options.second_font_size || settings.theme_options.second_font_size == undefined %]">[% second_font_size_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div ng-if="extra.theme_skins[settings.theme_skin].params.options.second_font_weight">
                              <label class="form-label m-t-15" for="theme-second-font-weight">
                                <span class="help">
                                  {t}Secondary font weight{/t}
                                </span>
                              </label>
                              <div class="controls">
                                <div class="input-group">
                                  <select id="theme-second-font-weight" name="theme-second-font-weight" ng-model="settings.theme_options.second_font_weight">
                                    <option value="[% second_font_weight_name %]" ng-repeat="(second_font_weight_name,second_font_weight_value) in extra.theme_skins[settings.theme_skin].params.options.second_font_weight.options" ng-selected="[% second_font_weight_name === settings.theme_options.second_font_weight || settings.theme_options.second_font_weight == undefined %]">[% second_font_weight_value %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
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
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-code-fork"></i>
                          {t}Breadcrumb{/t}
                        </h4>
                        <div class="controls">
                          <div class="checkbox p-b-10">
                            <input id="theme-breadcrumb" name="theme-breadcrumb" ng-model="settings.theme_options.breadcrumb" ng-checked="[% settings.theme_options.breadcrumb != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                            <label for="theme-breadcrumb">
                              <span class="help">
                                {t}Set display of routes{/t}
                              </span>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-puzzle-piece"></i>
                          {t}Pre-main widget{/t}
                        </h4>
                        <label class="form-label" for="theme-general-main-widget">
                          <span class="help">
                            {t}Widget to show before main content{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-addon">
                              ID
                            </span>
                            <input class="form-control" id="general-main-widget" name="general-main-widget" ng-model="settings.theme_options.general_main_widget" type="text">
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-puzzle-piece"></i>
                          {t}Pre-footer widget{/t}
                        </h4>
                        <label class="form-label" for="theme-general-footer-widget">
                          <span class="help">
                            {t}Widget to show before footer{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-addon">
                              ID
                            </span>
                            <input class="form-control" id="general-footer-widget" name="general-footer-widget" ng-model="settings.theme_options.general_footer_widget" type="text">
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
                      <div class="col-xs-12 col-md-5 m-b-15">
                        <label class="form-label" for="theme-option-author-photo-crop">
                          <span class="help">
                            {t}Author's photo crop{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-option-author-photo-crop" name="theme-option-author-photo-crop" ng-model="settings.theme_options.content_author_photo_crop">
                              <option value="[% content_author_photo_crop_name %]" ng-repeat="(content_author_photo_crop_name,content_author_photo_crop_value) in extra.theme_skins[settings.theme_skin].params.options.content_author_photo_crop.options" ng-selected="[% content_author_photo_crop_name === settings.theme_options.content_author_photo_crop %]">[% content_author_photo_crop_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-12 m-b-15">
                        <label class="form-label m-b-15" for="theme-option-content-imageratio-tiny">
                          <h4>
                            <i class="fa fa-code"></i>
                            Custom CSS
                          </h4>
                        </label>
                        <div class="form-group">
                          <label class="form-label" for="custom-theme-css">
                            <span class="help">{t}This css will be added at the end of the original{/t}</span>
                          </label>
                          <div class="controls">
                            <textarea class="form-control" id="custom-theme-css" name="theme-option-custom-css" ng-model="settings.custom_css" rows="15"></textarea>
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
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-minus"></i>
                          {t}Topbar{/t}
                        </h4>
                        <div class="controls">
                          <div class="checkbox p-b-10">
                            <input id="theme-general-topbar" name="theme-general-topbar" ng-model="settings.theme_options.general_topbar" ng-checked="[% settings.theme_options.general_topbar != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                            <label for="theme-general-topbar">
                              <span class="help">
                                {t}Set display of header's top bar{/t}
                              </span>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-calendar"></i>
                          {t}Header date{/t}
                        </h4>
                        <div class="controls">
                          <div class="checkbox p-b-10">
                            <input id="theme-general-header-date" name="theme-general-header-date" ng-model="settings.theme_options.general_header_date" ng-checked="[% settings.theme_options.general_header_date != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                            <label for="theme-general-header-date">
                              <span class="help">
                                {t}Set display of date in header's topbar{/t}
                              </span>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-minus"></i>
                          {t}Progress bar{/t}
                        </h4>
                        <div class="controls">
                          <div class="checkbox p-b-10">
                            <input id="theme-progressbar" name="theme-progressbar" ng-model="settings.theme_options.progressbar" ng-checked="[% settings.theme_options.progressbar != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                            <label for="theme-progressbar">
                              <span class="help">
                                {t}Set display of scroll progress bar{/t}
                              </span>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-b-15">
                        <h4>
                          <i class="fa fa-puzzle-piece"></i>
                          {t}Header right widget{/t}
                        </h4>
                        <label class="form-label" for="theme-general-header-right-widget">
                          <span class="help">
                            {t}Widget to show before main content{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-addon">
                              ID
                            </span>
                            <input class="form-control" id="general-header-right-widget" name="general-header-right-widget" ng-model="settings.theme_options.general_header_right_widget" type="text">
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
                          <div class="row border p-15">
                            <div class="col-xs-12 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-content-category-name">
                                <h4>
                                  <i class="fa fa-folder"></i>
                                  {t}Default contents{/t}
                                </h4>
                              </label>
                              <img src="/themes/apolo/images/admin/theme-settings-content-normal.jpg" alt="{t}Default contents{/t}" class="img img-responsive img-rounded m-b-10">
                            </div>
                            <div class="col-xs-12 m-b-5">
                              <div class="row">
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-category-name" name="theme-option-content-category-name" ng-model="settings.theme_options.content_category_name" ng-checked="[% settings.theme_options.content_category_name != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-category-name">
                                        <i class="fa fa-folder"></i>
                                        <span class="help">
                                          {t}Pretitle{/t}/{t}Category{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-subtitle" name="theme-option-content-subtitle" ng-model="settings.theme_options.content_subtitle" ng-checked="[% settings.theme_options.content_subtitle != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-subtitle">
                                        <i class="fa fa-header"></i>
                                        <span class="help">
                                          {t}Subtitle{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12">
                                  <div class="row">
                                    <div class="col-xs-12 m-b-5 border-bottom">
                                      <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                        <div class="checkbox p-b-10">
                                          <input id="theme-option-content-summary" name="theme-option-content-summary" ng-model="settings.theme_options.content_summary" ng-checked="[% settings.theme_options.content_summary != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                          <label class="form-label m-b-15" for="theme-option-content-summary">
                                            <i class="fa fa-align-left"></i>
                                            <span class="help">
                                              {t}Summary{/t}/{t}Description{/t}
                                            </span>
                                          </label>
                                        </div>
                                      </div>
                                      <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                        <div class="checkbox p-b-10">
                                          <input id="theme-option-content-summary-forced" name="theme-option-content-summary-forced" ng-model="settings.theme_options.content_summary_forced" ng-checked="[% settings.theme_options.content_summary_forced == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_summary === 'true'"/>
                                          <input id="theme-option-content-summary-forced" name="theme-option-content-summary-forced" ng-model="settings.theme_options.content_summary_forced" ng-checked="[% settings.theme_options.content_summary_forced == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_summary !== 'true'"/>
                                          <label class="form-label m-b-15" for="theme-option-content-summary-forced">
                                            <span class="help">
                                              {t}Extract from body if needed{/t}
                                            </span>
                                          </label>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-author" name="theme-option-content-author" ng-model="settings.theme_options.content_author" ng-checked="[% settings.theme_options.content_author != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-author">
                                        <i class="fa fa-address-card"></i>
                                        <span class="help">
                                          {t}Author{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-author-photo" name="theme-option-content-author-photo" ng-model="settings.theme_options.content_author_photo" ng-checked="[% settings.theme_options.content_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_author === 'true'"/>
                                      <input id="theme-option-content-author-photo" name="theme-option-content-author-photo" ng-model="settings.theme_options.content_author_photo" ng-checked="[% settings.theme_options.content_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_author !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-author-photo">
                                          <i class="fa fa-user"></i>
                                        <span class="help">
                                          {t}Display author's photo{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-author-bio" name="theme-option-content-author-bio" ng-model="settings.theme_options.content_author_bio" ng-checked="[% settings.theme_options.content_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_author === 'true'"/>
                                      <input id="theme-option-content-author-bio" name="theme-option-content-author-bio" ng-model="settings.theme_options.content_author_bio" ng-checked="[% settings.theme_options.content_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_author !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-author-bio">
                                        <i class="fa fa-plus-circle"></i>
                                        <span class="help">
                                          {t}Display author's bio{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5">
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-date" name="theme-option-content-date" ng-model="settings.theme_options.content_date" ng-checked="[% settings.theme_options.content_date != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-date">
                                        <i class="fa fa-calendar"></i>
                                        <span class="help">
                                          {t}Date{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-hour" name="theme-option-content-hour" ng-model="settings.theme_options.content_time" ng-checked="[% settings.theme_options.content_time != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_date === 'true'"/>
                                      <input id="theme-option-content-hour" name="theme-option-content-hour" ng-model="settings.theme_options.content_time" ng-checked="[% settings.theme_options.content_time != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_date !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-hour">
                                        <i class="fa fa-clock-o"></i>
                                        <span class="help">
                                          {t}Hour{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-readtime" name="theme-option-content-readtime" ng-model="settings.theme_options.content_readtime" ng-checked="[% settings.theme_options.content_readtime != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-readtime">
                                        <i class="fa fa-coffee"></i>
                                        <span class="help">
                                          {t}Display content's readtime{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-score" name="theme-option-content-score" ng-model="settings.theme_options.content_score" ng-checked="[% settings.theme_options.content_score != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-score">
                                        <i class="fa fa-futbol-o"></i>
                                        <span class="help">
                                          {t}Display sports scoreboard{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <div class="row border p-15">
                            <div class="col-xs-12 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-content-tiny-category-name">
                                <h4>
                                  <i class="fa fa-folder"></i>
                                  {t}Tiny contents{/t}
                                </h4>
                              </label>
                              <img src="/themes/apolo/images/admin/theme-settings-content-tiny.jpg" alt="{t}Tiny contents{/t}" class="img img-responsive img-rounded m-b-10">
                            </div>
                            <div class="col-xs-12 m-b-5">
                              <div class="row">
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-category-name" name="theme-option-content-tiny-category-name" ng-model="settings.theme_options.content_tiny_category_name" ng-checked="[% settings.theme_options.content_tiny_category_name == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-category-name">
                                        <i class="fa fa-folder"></i>
                                        <span class="help">
                                          {t}Pretitle{/t}/{t}Category{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-subtitle" name="theme-option-content-tiny-subtitle" ng-model="settings.theme_options.content_tiny_subtitle" ng-checked="[% settings.theme_options.content_tiny_subtitle == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-subtitle">
                                        <i class="fa fa-header"></i>
                                        <span class="help">
                                          {t}Subtitle{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12">
                                  <div class="row">
                                    <div class="col-xs-12 m-b-5 border-bottom">
                                      <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                        <div class="checkbox p-b-10">
                                          <input id="theme-option-content-tiny-summary" name="theme-option-content-tiny-summary" ng-model="settings.theme_options.content_tiny_summary" ng-checked="[% settings.theme_options.content_tiny_summary == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                          <label class="form-label m-b-15" for="theme-option-content-tiny-summary">
                                            <i class="fa fa-align-left"></i>
                                            <span class="help">
                                              {t}Summary{/t}/{t}Description{/t}
                                            </span>
                                          </label>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-author" name="theme-option-content-tiny-author" ng-model="settings.theme_options.content_tiny_author" ng-checked="[% settings.theme_options.content_tiny_author == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-author">
                                        <i class="fa fa-address-card"></i>
                                        <span class="help">
                                          {t}Author{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-author-photo" name="theme-option-content-tiny-author-photo" ng-model="settings.theme_options.content_tiny_author_photo" ng-checked="[% settings.theme_options.content_tiny_author_photo == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_tiny_author === 'true'"/>
                                      <input id="theme-option-content-tiny-author-photo" name="theme-option-content-tiny-author-photo" ng-model="settings.theme_options.content_tiny_author_photo" ng-checked="[% settings.theme_options.content_tiny_author_photo == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_tiny_author !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-author-photo">
                                          <i class="fa fa-user"></i>
                                        <span class="help">
                                          {t}Display author's photo{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-author-bio" name="theme-option-content-tiny-author-bio" ng-model="settings.theme_options.content_tiny_author_bio" ng-checked="[% settings.theme_options.content_tiny_author_bio == 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_tiny_author === 'true'"/>
                                      <input id="theme-option-content-tiny-author-bio" name="theme-option-content-tiny-author-bio" ng-model="settings.theme_options.content_tiny_author_bio" ng-checked="[% settings.theme_options.content_tiny_author_bio == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_tiny_author !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-author-bio">
                                        <i class="fa fa-plus-circle"></i>
                                        <span class="help">
                                          {t}Display author's bio{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5">
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-date" name="theme-option-content-tiny-date" ng-model="settings.theme_options.content_tiny_date" ng-checked="[% settings.theme_options.content_tiny_date == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-date">
                                        <i class="fa fa-calendar"></i>
                                        <span class="help">
                                          {t}Date{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-hour" name="theme-option-content-tiny-hour" ng-model="settings.theme_options.content_tiny_time" ng-checked="[% settings.theme_options.content_tiny_time == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_tiny_date === 'true'"/>
                                      <input id="theme-option-content-tiny-hour" name="theme-option-content-tiny-hour" ng-model="settings.theme_options.content_tiny_time" ng-checked="[% settings.theme_options.content_tiny_time == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_tiny_date !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-hour">
                                        <i class="fa fa-clock-o"></i>
                                        <span class="help">
                                          {t}Hour{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-readtime" name="theme-option-content-tiny-readtime" ng-model="settings.theme_options.content_tiny_readtime" ng-checked="[% settings.theme_options.content_tiny_readtime != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-readtime">
                                        <i class="fa fa-coffee"></i>
                                        <span class="help">
                                          {t}Display content's readtime{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-tiny-score" name="theme-option-content-tiny-score" ng-model="settings.theme_options.content_tiny_score" ng-checked="[% settings.theme_options.content_tiny_score != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-tiny-score">
                                        <i class="fa fa-futbol-o"></i>
                                        <span class="help">
                                          {t}Display sports scoreboard{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <div class="row border p-15">
                            <div class="col-xs-12 m-b-15">
                              <label class="form-label m-b-15" for="theme-option-content-over-category-name">
                                <h4>
                                  <i class="fa fa-folder"></i>
                                  {t}Over contents{/t}
                                </h4>
                              </label>
                              <img src="/themes/apolo/images/admin/theme-settings-content-over.jpg" alt="{t}Over contents{/t}" class="img img-responsive img-rounded m-b-10">
                            </div>
                            <div class="col-xs-12 m-b-5">
                              <div class="row">
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-category-name" name="theme-option-content-over-category-name" ng-model="settings.theme_options.content_over_category_name" ng-checked="[% settings.theme_options.content_over_category_name == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-category-name">
                                        <i class="fa fa-folder"></i>
                                        <span class="help">
                                          {t}Pretitle{/t}/{t}Category{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-subtitle" name="theme-option-content-over-subtitle" ng-model="settings.theme_options.content_over_subtitle" ng-checked="[% settings.theme_options.content_over_subtitle == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-subtitle">
                                        <i class="fa fa-header"></i>
                                        <span class="help">
                                          {t}Subtitle{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12">
                                  <div class="row">
                                    <div class="col-xs-12 m-b-5 border-bottom">
                                      <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                        <div class="checkbox p-b-10">
                                          <input id="theme-option-content-over-summary" name="theme-option-content-over-summary" ng-model="settings.theme_options.content_over_summary" ng-checked="[% settings.theme_options.content_over_summary == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                          <label class="form-label m-b-15" for="theme-option-content-over-summary">
                                            <i class="fa fa-align-left"></i>
                                            <span class="help">
                                              {t}Summary{/t}/{t}Description{/t}
                                            </span>
                                          </label>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5 border-bottom">
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-author" name="theme-option-content-over-author" ng-model="settings.theme_options.content_over_author" ng-checked="[% settings.theme_options.content_over_author == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-author">
                                        <i class="fa fa-address-card"></i>
                                        <span class="help">
                                          {t}Author{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-author-photo" name="theme-option-content-over-author-photo" ng-model="settings.theme_options.content_over_author_photo" ng-checked="[% settings.theme_options.content_over_author_photo == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_over_author === 'true'"/>
                                      <input id="theme-option-content-over-author-photo" name="theme-option-content-over-author-photo" ng-model="settings.theme_options.content_over_author_photo" ng-checked="[% settings.theme_options.content_over_author_photo == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_over_author !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-author-photo">
                                          <i class="fa fa-user"></i>
                                        <span class="help">
                                          {t}Display author's photo{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-author-bio" name="theme-option-content-over-author-bio" ng-model="settings.theme_options.content_over_author_bio" ng-checked="[% settings.theme_options.content_over_author_bio == 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_over_author === 'true'"/>
                                      <input id="theme-option-content-over-author-bio" name="theme-option-content-over-author-bio" ng-model="settings.theme_options.content_over_author_bio" ng-checked="[% settings.theme_options.content_over_author_bio == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_over_author !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-author-bio">
                                        <i class="fa fa-plus-circle"></i>
                                        <span class="help">
                                          {t}Display author's bio{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-xs-12 m-b-5">
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-date" name="theme-option-content-over-date" ng-model="settings.theme_options.content_over_date" ng-checked="[% settings.theme_options.content_over_date == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-date">
                                        <i class="fa fa-calendar"></i>
                                        <span class="help">
                                          {t}Date{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-hour" name="theme-option-content-over-hour" ng-model="settings.theme_options.content_over_time" ng-checked="[% settings.theme_options.content_over_time == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_over_date === 'true'"/>
                                      <input id="theme-option-content-over-hour" name="theme-option-content-over-hour" ng-model="settings.theme_options.content_over_time" ng-checked="[% settings.theme_options.content_over_time == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_over_date !== 'true'"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-hour">
                                        <i class="fa fa-clock-o"></i>
                                        <span class="help">
                                          {t}Hour{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-readtime" name="theme-option-content-over-readtime" ng-model="settings.theme_options.content_over_readtime" ng-checked="[% settings.theme_options.content_over_readtime != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-readtime">
                                        <i class="fa fa-coffee"></i>
                                        <span class="help">
                                          {t}Display content's readtime{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
                                  <div class="controls visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">
                                    <div class="checkbox p-b-10">
                                      <input id="theme-option-content-over-score" name="theme-option-content-over-score" ng-model="settings.theme_options.content_over_score" ng-checked="[% settings.theme_options.content_over_score != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                      <label class="form-label m-b-15" for="theme-option-content-over-score">
                                        <i class="fa fa-futbol-o"></i>
                                        <span class="help">
                                          {t}Display sports scoreboard{/t}
                                        </span>
                                      </label>
                                    </div>
                                  </div>
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
                          </label>
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-archive-category-name" name="theme-option-archive-category-name" ng-model="settings.theme_options.archive_category_name" ng-checked="[% settings.theme_options.archive_category_name != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label class="form-label m-b-15" for="theme-option-archive-category-name">
                                <span class="help">
                                  {t}Display pretitle or category in contents{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-subtitle">
                            <h4>
                              <i class="fa fa-header"></i>
                              {t}Subtitle{/t}
                            </h4>
                          </label>
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-archive-subtitle" name="theme-option-archive-subtitle" ng-model="settings.theme_options.archive_subtitle" ng-checked="[% settings.theme_options.archive_subtitle != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label class="form-label m-b-15" for="theme-option-archive-subtitle">
                                <span class="help">
                                  {t}Display subtitle{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-summary">
                            <h4>
                              <i class="fa fa-align-left"></i>
                              {t}Summary{/t}
                            </h4>
                          </label>
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-archive-summary" name="theme-option-archive-summary" ng-model="settings.theme_options.archive_summary" ng-checked="[% settings.theme_options.archive_summary != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label class="form-label m-b-15" for="theme-option-archive-summary">
                                <span class="help">
                                  {t}Display summary/description{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-secondary-menu">
                            <h4>
                              <i class="fa fa-navicon"></i>
                              {t}Secondary menu{/t}
                            </h4>
                          </label>
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-archive-secondary-menu" name="theme-option-archive-secondary-menu" ng-model="settings.theme_options.archive_secondary_menu" ng-checked="[% settings.theme_options.archive_secondary_menu == 'true' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label class="form-label m-b-15" for="theme-option-archive-secondary-menu">
                                <span class="help">
                                  {t}Display subcategories menu if exists{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 m-b-15">
                          <label class="form-label m-b-15" for="theme-option-archive-info">
                            <h4>
                              <i class="fa fa-info"></i>
                              {t}Info{/t}
                            </h4>
                          </label>
                        </div>
                        <div class="col-xs-12">
                          <div class="row">
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-author" name="theme-option-archive-author" ng-model="settings.theme_options.archive_author" ng-checked="[% settings.theme_options.archive_author != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label class="form-label m-b-15" for="theme-option-archive-author">
                                    <span class="help">
                                      {t}Display content's author{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-author-photo" name="theme-option-archive-author-photo" ng-model="settings.theme_options.archive_author_photo" ng-checked="[% settings.theme_options.archive_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.archive_author === 'true'"/>
                                  <input id="theme-option-archive-author-photo" name="theme-option-archive-author-photo" ng-model="settings.theme_options.archive_author_photo" ng-checked="[% settings.theme_options.archive_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.archive_author !== 'true'"/>
                                  <label class="form-label m-b-15" for="theme-option-archive-author-photo">
                                    <span class="help">
                                      {t}Display author's photo{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-author-bio" name="theme-option-archive-author-bio" ng-model="settings.theme_options.archive_author_bio" ng-checked="[% settings.theme_options.archive_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.archive_author === 'true'"/>
                                  <input id="theme-option-archive-author-bio" name="theme-option-archive-author-bio" ng-model="settings.theme_options.archive_author_bio" ng-checked="[% settings.theme_options.archive_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.archive_author !== 'true'"/>
                                  <label class="form-label m-b-15" for="theme-option-archive-author-bio">
                                    <span class="help">
                                      {t}Display author's bio{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-date" name="theme-option-archive-date" ng-model="settings.theme_options.archive_date" ng-checked="[% settings.theme_options.archive_date != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label class="form-label m-b-15" for="theme-option-archive-date">
                                    <span class="help">
                                      {t}Display content's date{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-time" name="theme-option-archive-time" ng-model="settings.theme_options.archive_time" ng-checked="[% settings.theme_options.archive_time != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.archive_date === 'true'"/>
                                  <input id="theme-option-archive-time" name="theme-option-archive-time" ng-model="settings.theme_options.archive_time" ng-checked="[% settings.theme_options.archive_time != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.archive_date !== 'true'"/>
                                  <label class="form-label m-b-15" for="theme-option-archive-time">
                                    <span class="help">
                                      {t}Display content's time{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-readtime" name="theme-option-archive-readtime" ng-model="settings.theme_options.archive_readtime" ng-checked="[% settings.theme_options.archive_readtime != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label class="form-label m-b-15" for="theme-option-archive-readtime">
                                    <span class="help">
                                      {t}Display content's readtime{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
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
                              <input id="theme-option-sidebar-widget-today-news-list" name="theme-option-sidebar-widget-today-news-list" ng-model="settings.theme_options.sidebar_widget_today_news_list" ng-checked="[% settings.theme_options.sidebar_widget_today_news_list != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label for="theme-option-sidebar-widget-today-news-list">
                                <span>{t domain="base"}Today news{/t}</span>
                              </label>
                            </div>
                            <div class="checkbox p-b-10">
                              <input id="theme-option-sidebar-widget-most-viewed-list" name="theme-option-sidebar-widget-most-viewed-list" ng-model="settings.theme_options.sidebar_widget_most_viewed_list" ng-checked="[% settings.theme_options.sidebar_widget_most_viewed_list != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label for="theme-option-sidebar-widget-most-viewed-list">
                                <span>{t domain="base"}Most viewed{/t}</span>
                              </label>
                            </div>
                            <div class="checkbox p-b-10">
                              <input id="theme-option-sidebar-widget-most-seeing-list" name="theme-option-sidebar-widget-most-seeing-list" ng-model="settings.theme_options.sidebar_widget_most_seeing_recent_list" ng-checked="[% settings.theme_options.sidebar_widget_most_seeing_recent_list %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label for="theme-option-sidebar-widget-most-seeing-list">
                                <span>{t domain="base"}Most seeing/recent{/t}</span>
                              </label>
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
                            <label class="form-label m-b-15" for="theme-option-event-info-display">
                              <h4>
                                <i class="fa fa-calendar"></i>
                                {t}Events info{/t}
                              </h4>
                              <span class="help">
                                {t}Choose where to display{/t}
                              </span>
                            </label>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-option-event-info-display" name="theme-option-event-info-display" ng-model="settings.theme_options.event_info_display">
                                  <option value="[% event_info_display_name %]" ng-repeat="(event_info_display_name,event_info_display_value) in extra.theme_skins[settings.theme_skin].params.options.event_info_display.options" ng-selected="[% event_info_display_name === settings.theme_options.event_info_display || settings.theme_options.event_info_display == undefined %]">[% event_info_display_value %]</option>
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
                          <div class="col-xs-12">
                            <div class="col-xs-12 col-md-12">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-inner-content-author" name="theme-option-inner_content-author" ng-model="settings.theme_options.inner_content_author" ng-checked="[% settings.theme_options.inner_content_author != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label class="form-label m-b-15" for="theme-option-inner-content-author">
                                    <span class="help">
                                      {t}Display content's author{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-12">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-inner-content-author-photo" name="theme-option-inner_content-author-photo" ng-model="settings.theme_options.inner_content_author_photo" ng-checked="[% settings.theme_options.inner_content_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.inner_content_author === 'true'"/>
                                  <input id="theme-option-inner-content-author-photo" name="theme-option-inner_content-author-photo" ng-model="settings.theme_options.inner_content_author_photo" ng-checked="[% settings.theme_options.inner_content_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.inner_content_author !== 'true'"/>
                                  <label class="form-label m-b-15" for="theme-option-inner-content-author-photo">
                                    <span class="help">
                                      {t}Display author's photo{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-12">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-inner-content-author-bio" name="theme-option-inner_content-author-bio" ng-model="settings.theme_options.inner_content_author_bio" ng-checked="[% settings.theme_options.inner_content_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.inner_content_author === 'true'"/>
                                  <input id="theme-option-inner-content-author-bio" name="theme-option-inner_content-author-bio" ng-model="settings.theme_options.inner_content_author_bio" ng-checked="[% settings.theme_options.inner_content_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.inner_content_author !== 'true'"/>
                                  <label class="form-label m-b-15" for="theme-option-inner-content-author-bio">
                                    <span class="help">
                                      {t}Display author's bio{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-12">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-inner-content-date" name="theme-option-inner_content-date" ng-model="settings.theme_options.inner_content_date" ng-checked="[% settings.theme_options.inner_content_date != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label class="form-label m-b-15" for="theme-option-inner-content-date">
                                    <span class="help">
                                      {t}Display content's date{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-12">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-inner-content-time" name="theme-option-inner_content-time" ng-model="settings.theme_options.inner_content_time" ng-checked="[% settings.theme_options.inner_content_time != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.inner_content_date === 'true'"/>
                                  <input id="theme-option-inner-content-time" name="theme-option-inner_content-time" ng-model="settings.theme_options.inner_content_time" ng-checked="[% settings.theme_options.inner_content_time != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.inner_content_date !== 'true'"/>
                                  <label class="form-label m-b-15" for="theme-option-inner-content-time">
                                    <span class="help">
                                      {t}Display content's time{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-12">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-inner-content-readtime" name="theme-option-inner_content-readtime" ng-model="settings.theme_options.inner_content_readtime" ng-checked="[% settings.theme_options.inner_content_readtime != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label class="form-label m-b-15" for="theme-option-inner-content-readtime">
                                    <span class="help">
                                      {t}Display content's readtime{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-12 m-b-15">
                            <label class="form-label m-b-15" for="theme-option-sidebar-widgets">
                              <h4>
                                <i class="fa fa-puzzle-piece"></i>
                                {t}Sidebar widgets{/t}
                              </h4>
                            </label>
                            <label class="form-label" for="theme-sidebar-widget-custom">
                              <span class="help">
                                {t}Show custom widget{/t}
                              </span>
                            </label>
                            <div class="controls m-b-15">
                              <div class="input-group">
                                <span class="input-group-addon">
                                  ID
                                </span>
                                <input class="form-control" id="sidebar-widget-custom" name="sidebar-widget-custom" ng-model="settings.theme_options.sidebar_widget_custom" type="text">
                              </div>
                            </div>
                            <label>
                              <span class="help">
                                {t}Choose widgets to display{/t}
                              </span>
                            </label>
                            <div class="form-group m-b-0">
                              <div class="checkbox p-b-10">
                                <input id="theme-option-sidebar-widget-today-news-inner" name="theme-option-sidebar-widget-today-news-inner" ng-model="settings.theme_options.sidebar_widget_today_news_inner" ng-checked="[% settings.theme_options.sidebar_widget_today_news_inner != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-sidebar-widget-today-news-inner">
                                  <span>{t domain="base"}Today news{/t}</span>
                                </label>
                              </div>
                              <div class="checkbox p-b-10">
                                <input id="theme-option-sidebar-widget-most-viewed-inner" name="theme-option-sidebar-widget-most-viewed-inner" ng-model="settings.theme_options.sidebar_widget_most_viewed_inner" ng-checked="[% settings.theme_options.sidebar_widget_most_viewed_inner != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-sidebar-widget-most-viewed-inner">
                                  <span>{t domain="base"}Most viewed{/t}</span>
                                </label>
                              </div>
                              <div class="checkbox p-b-10">
                                <input id="theme-option-sidebar-widget-most-seeing-inner" name="theme-option-sidebar-widget-most-seeing-inner" ng-model="settings.theme_options.sidebar_widget_most_seeing_recent_inner" ng-checked="[% settings.theme_options.sidebar_widget_most_seeing_recent_inner %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                <label for="theme-option-sidebar-widget-most-seeing-inner">
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
                  <div role="tabpanel" class="tab-pane fade" id="tabOpinion">
                    <div ng-if="extra.theme_skins[settings.theme_skin].params.options">
                      <div class="row m-b-30">
                        <div class="col-xs-12 m-b-10">
                          <label class="form-label" for="theme-option-opinon-home">
                            <h4>
                              <i class="fa fa-newspaper-o"></i>
                              {t}Home{/t}
                            </h4>
                            <span class="help">
                              {t}Choose options for contents{/t}
                            </span>
                          </label>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-content-opinion-media" name="theme-option-content-opinion-media" ng-model="settings.theme_options.content_opinion_media" ng-checked="[% settings.theme_options.content_opinion_media != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label for="theme-option-content-opinion-media">
                                <span>
                                  <i class="fa fa-picture-o"></i>
                                  <strong>{t}Media{/t}</strong><br>
                                  {t}Display featured media in opinions{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-content-opinion-summary" name="theme-option-content-opinion-summary" ng-model="settings.theme_options.content_opinion_summary" ng-checked="[% settings.theme_options.content_opinion_summary != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label for="theme-option-content-opinion-summary">
                                <span>
                                  <i class="fa fa-align-left"></i>
                                  <strong>{t}Summary{/t}</strong><br>
                                  {t}Display summary/description{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12">
                          <div class="row">
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-content-opinion-author" name="theme-option-content-opinion-author" ng-model="settings.theme_options.content_opinion_author" ng-checked="[% settings.theme_options.content_opinion_author != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label for="theme-option-content-opinion-author">
                                    <span>
                                      <i class="fa fa-address-card"></i>
                                      {t}Display content's author{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" >
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-content-opinion-author-photo" name="theme-option-content-opinion-author-photo" ng-model="settings.theme_options.content_opinion_author_photo" ng-checked="[% settings.theme_options.content_opinion_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_opinion_author === 'true'"/>
                                  <input id="theme-option-content-opinion-author-photo" name="theme-option-content-opinion-author-photo" ng-model="settings.theme_options.content_opinion_author_photo" ng-checked="[% settings.theme_options.content_opinion_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_opinion_author !== 'true'"/>
                                  <label for="theme-option-content-opinion-author-photo">
                                    <span>
                                      <i class="fa fa-user"></i>
                                      {t}Display author's photo{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" >
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-content-opinion-author-bio" name="theme-option-content-opinion-author-bio" ng-model="settings.theme_options.content_opinion_author_bio" ng-checked="[% settings.theme_options.content_opinion_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.content_opinion_author === 'true'"/>
                                  <input id="theme-option-content-opinion-author-bio" name="theme-option-content-opinion-author-bio" ng-model="settings.theme_options.content_opinion_author_bio" ng-checked="[% settings.theme_options.content_opinion_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.content_opinion_author !== 'true'"/>
                                  <label for="theme-option-content-opinion-author-bio">
                                    <span>
                                      <i class="fa fa-plus-circle"></i>
                                      {t}Display author's bio{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row m-b-30">
                        <div class="col-xs-12 m-b-10">
                          <label class="form-label m-b-10" for="theme-option-opinon-home">
                            <h4>
                              <i class="fa fa-minus"></i>
                              {t}{t domain="base"}Listings/Archive{/t}{/t}
                            </h4>
                            <span class="help">
                              {t}Choose options for contents{/t}
                            </span>
                          </label>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-archive-opinion-media" name="theme-option-archive-opinion-media" ng-model="settings.theme_options.archive_opinion_media" ng-checked="[% settings.theme_options.archive_opinion_media != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label for="theme-option-archive-opinion-media">
                                <span>
                                  <i class="fa fa-picture-o"></i>
                                  <strong>{t}Media{/t}</strong><br>
                                  {t}Display featured media in opinions{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-4 m-b-15">
                          <div class="controls">
                            <div class="checkbox p-b-10">
                              <input id="theme-option-archive-opinion-summary" name="theme-option-archive-opinion-summary" ng-model="settings.theme_options.archive_opinion_summary" ng-checked="[% settings.theme_options.archive_opinion_summary != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                              <label for="theme-option-archive-opinion-summary">
                                <span>
                                  <i class="fa fa-align-left"></i>
                                  <strong>{t}Summary{/t}</strong><br>
                                  {t}Display summary/description{/t}
                                </span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12">
                          <div class="row">
                            <div class="col-xs-12 col-md-4 m-b-15">
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-opinion-author" name="theme-option-archive-opinion-author" ng-model="settings.theme_options.archive_opinion_author" ng-checked="[% settings.theme_options.archive_opinion_author != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox"/>
                                  <label for="theme-option-archive-opinion-author">
                                    <span>
                                      <i class="fa fa-address-card"></i>
                                      {t}Display content's author{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" >
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-opinion-author-photo" name="theme-option-archive-opinion-author-photo" ng-model="settings.theme_options.archive_opinion_author_photo" ng-checked="[% settings.theme_options.archive_opinion_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.archive_opinion_author === 'true'"/>
                                  <input id="theme-option-archive-opinion-author-photo" name="theme-option-archive-opinion-author-photo" ng-model="settings.theme_options.archive_opinion_author_photo" ng-checked="[% settings.theme_options.archive_opinion_author_photo != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.archive_opinion_author !== 'true'"/>
                                  <label for="theme-option-archive-opinion-author-photo">
                                    <span>
                                      <i class="fa fa-user"></i>
                                      {t}Display author's photo{/t}
                                    </span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="col-xs-12 col-md-4 m-b-15" >
                              <div class="controls">
                                <div class="checkbox p-b-10">
                                  <input id="theme-option-archive-opinion-author-bio" name="theme-option-archive-opinion-author-bio" ng-model="settings.theme_options.archive_opinion_author_bio" ng-checked="[% settings.theme_options.archive_opinion_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" ng-if="settings.theme_options.archive_opinion_author === 'true'"/>
                                  <input id="theme-option-archive-opinion-author-bio" name="theme-option-archive-opinion-author-bio" ng-model="settings.theme_options.archive_opinion_author_bio" ng-checked="[% settings.theme_options.archive_opinion_author_bio != 'false' %]" ng-true-value="'true'" ng-false-value="'false'" type="checkbox" disabled ng-if="settings.theme_options.archive_opinion_author !== 'true'"/>
                                  <label for="theme-option-archive-opinion-author-bio">
                                    <span>
                                      <i class="fa fa-plus-circle"></i>
                                      {t}Display author's bio{/t}
                                    </span>
                                  </label>
                                </div>
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
                      <div class="col-xs-12 col-md-4 m-b-15">
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
