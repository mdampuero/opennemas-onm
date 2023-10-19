{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="MasterSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-eye fa-lg"></i>
                {t}TRheme settings{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
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
                  <div ng-if="extra.theme_skins[settings.theme_skin].params.fonts">
                    <div class="row m-b-15">
                      <div class="col-xs-12">
                        <h4>
                          <i class="fa fa-font"></i>
                          {t}Theme fonts{/t}
                        </h4>
                      </div>
                      <div class="col-xs-12 col-md-6">
                        <label class="form-label" for="theme-font">
                          <span class="help">
                            {t}Your theme offers multiple fonts to match your page style. Select yout main font (titles, headings…).{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-font" name="theme-font" ng-model="settings.theme_font" required>
                              <option value="[% font_name %]" ng-repeat="(font_name,font_url) in extra.theme_skins[settings.theme_skin].params.fonts" ng-selected="[% font_name === settings.theme_font || settings.theme_font == undefined %]">[% font_url %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-6">
                        <label class="form-label m-t-15" for="theme-font-secondary">
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
                      </div>
                    </div>
                    <div class="row m-b-15">
                      <div class="col-xs-12">
                        <h4>
                          <i class="fa fa-columns"></i>
                          {t}Content Layout{/t}
                        </h4>
                      </div>
                      <div class="col-xs-12">
                        <label class="form-label" for="theme-option-width">
                          <span class="help">
                            {t}Page containers width{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-option-width" name="theme-option-width" ng-model="settings.theme_options.general_page_width" required>
                              <option value="[% page_width_name %]" ng-repeat="(page_width_name,page_width_value) in extra.theme_skins[settings.theme_skin].params.options.option_general_page_width" ng-selected="[% page_width_name === settings.theme_options.general_page_width || settings.theme_options.general_page_width == undefined %]">[% page_width_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 m-t-15">
                        <label class="form-label" for="theme-option-media-header">
                          <span class="help">
                            {t}Featured media{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.article_header_media">
                            <div class="panel panel-default col-xs-10 col-md-3" ng-repeat="(header_media_name,header_media_value) in extra.theme_skins[settings.theme_skin].params.options.option_article_header_media">
                              <div class="radio">
                                <input id="theme-option-media-header-[% header_media_name %]" name="theme-option-media-header" ng-model="settings.theme_options.article_header_media" value="[% header_media_name %]" ng-checked="[% header_media_name === settings.theme_options.article_header_media %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-media-header-[% header_media_name %]">
                                  <img src="/themes/apolo/images/admin/article_header_media-[% header_media_name %].jpg" alt="[% header_media_name %]" class="img img-responsive img-rounded m-b-10">
                                  <h4>[% header_media_value %]</h4>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 m-t-15">
                        <label class="form-label" for="theme-option-order-header">
                          <span class="help">
                            {t}Content header order{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.article_header_order">
                            <div class="panel panel-default col-xs-10 col-md-3" ng-repeat="(header_order_name,header_order_value) in extra.theme_skins[settings.theme_skin].params.options.option_article_header_order">
                              <div class="radio">
                                <input id="theme-option-order-header-[% header_order_name %]" name="theme-option-order-header" ng-model="settings.theme_options.article_header_order" value="[% header_order_name %]" ng-checked="[% header_order_name === settings.theme_options.article_header_order %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-order-header-[% header_order_name %]">
                                  <img src="/themes/apolo/images/admin/article_header_order-[% header_order_name %].jpg" alt="[% header_order_name %]" class="img img-responsive img-rounded m-b-10">
                                  <h4>[% header_order_value %]</h4>
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
            </div>
          {/if}
        </div>
      </div>
    </div>
  </form>
{/block}
