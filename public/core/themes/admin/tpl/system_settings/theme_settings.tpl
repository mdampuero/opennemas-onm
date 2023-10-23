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
                {t}Theme settings{/t}
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
                      <div class="col-xs-12 col-md-4">
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
                        <label class="form-label m-t-15" for="theme-main-font-size">
                          <span class="help">
                            {t}Main font base size{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-main-font-size" name="theme-main-font-size" ng-model="settings.theme_options.theme_main_font_size" required>
                              <option value="[% main_font_size_name %]" ng-repeat="(main_font_size_name,main_font_size_value) in extra.theme_skins[settings.theme_skin].params.options.option_main_font_size" ng-selected="[% main_font_size_name === settings.theme_options.theme_main_font_size || settings.theme_options.theme_main_font_size == undefined %]">[% main_font_size_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4">
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
                            <select id="theme-second-font-size" name="theme-second-font-size" ng-model="settings.theme_options.theme_second_font_size" required>
                              <option value="[% second_font_size_name %]" ng-repeat="(second_font_size_name,second_font_size_value) in extra.theme_skins[settings.theme_skin].params.options.option_second_font_size" ng-selected="[% second_font_size_name === settings.theme_options.theme_second_font_size || settings.theme_options.theme_second_font_size == undefined %]">[% second_font_size_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row m-b-15">
                      <div class="col-xs-12 col-md-4">
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
                              <option value="[% page_width_name %]" ng-repeat="(page_width_name,page_width_value) in extra.theme_skins[settings.theme_skin].params.options.option_general_page_width" ng-selected="[% page_width_name === settings.theme_options.general_page_width || settings.theme_options.general_page_width == undefined %]">[% page_width_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4">
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
                              <option value="[% header_align_name %]" ng-repeat="(header_align_name,header_align_value) in extra.theme_skins[settings.theme_skin].params.options.option_header_align" ng-selected="[% header_align_name === settings.theme_options.header_align || settings.theme_options.header_align == undefined %]">[% header_align_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4">
                        <h4>
                          <i class="fa fa-paint-brush"></i>
                          {t}Main header color{/t}
                        </h4>
                        <label class="form-label" for="theme-header-color">
                          <span class="help">
                            {t}Choose header appearance{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <select id="theme-header-color" name="theme-header-color" ng-model="settings.theme_options.header_color" required>
                              <option value="[% header_color_name %]" ng-repeat="(header_color_name,header_color_value) in extra.theme_skins[settings.theme_skin].params.options.option_header_color" ng-selected="[% header_color_name === settings.theme_options.header_color || settings.theme_options.header_color == undefined %]">[% header_color_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4 m-t-15">
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
                              <option value="[% breadcrumb_name %]" ng-repeat="(breadcrumb_name,breadcrumb_value) in extra.theme_skins[settings.theme_skin].params.options.option_breadcrumb" ng-selected="[% breadcrumb_name === settings.theme_options.breadcrumb || settings.theme_options.breadcrumb == undefined %]">[% breadcrumb_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row m-b-15">
                      <div class="col-xs-12 m-t-15">
                        <label class="form-label m-b-15" for="theme-option-content-header">
                          <h4>
                            <i class="fa fa-align-center"></i>
                            {t}Content header{/t}
                          </h4>
                          <span class="help">
                            {t}Display inner contents header at full width or inbody column{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.content_header">
                            <div class="panel panel-default col-xs-5 col-md-2" ng-repeat="(content_header_name,content_header_value) in extra.theme_skins[settings.theme_skin].params.options.option_content_header">
                              <div class="radio">
                                <input id="theme-option-content-header-[% content_header_name %]" name="theme-option-content-header" ng-model="settings.theme_options.content_header" value="[% content_header_name %]" ng-checked="[% content_header_name === settings.theme_options.content_header %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-content-header-[% content_header_name %]">
                                  <img src="/themes/apolo/images/admin/content_header-[% content_header_name %].jpg" alt="[% content_header_name %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% content_header_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-12 m-t-15">
                        <label class="form-label m-b-15" for="theme-option-content-layout">
                          <h4>
                            <i class="fa fa-columns"></i>
                            {t}Sidebar{/t}
                          </h4>
                          <span class="help">
                            {t}Show or hide right sidebar{/t}
                          </span>
                        </label>
                        <div class="controls">
                          <div class="row" ng-model="settings.theme_options.content_layout">
                            <div class="panel panel-default col-xs-5 col-md-2" ng-repeat="(content_layout_name,content_layout_value) in extra.theme_skins[settings.theme_skin].params.options.option_content_layout">
                              <div class="radio">
                                <input id="theme-option-content-layout-[% content_layout_name %]" name="theme-option-content-layout" ng-model="settings.theme_options.content_layout" value="[% content_layout_name %]" ng-checked="[% content_layout_name === settings.theme_options.content_layout %]" type="radio"/>
                                <label class="no-radio m-l-0 p-l-15 p-r-15 p-t-15 p-b-15" for="theme-option-content-layout-[% content_layout_name %]">
                                  <img src="/themes/apolo/images/admin/content_layout-[% content_layout_name %].jpg" alt="[% content_layout_value %]" class="img img-responsive img-rounded m-b-10">
                                  <h5>[% content_layout_value %]</h5>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-12 m-t-15">
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
                            <div class="panel panel-default col-xs-5 col-md-2" ng-repeat="(header_media_name,header_media_value) in extra.theme_skins[settings.theme_skin].params.options.option_article_header_media">
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

                      <div class="col-xs-12 m-t-15" ng-if="settings.theme_options.article_header_media === 'header'">
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
                            <div class="panel panel-default col-xs-5 col-md-2" ng-repeat="(header_order_name,header_order_value) in extra.theme_skins[settings.theme_skin].params.options.option_article_header_order">
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

                      <div class="col-xs-12 m-t-15" ng-if="settings.theme_options.article_header_media === 'header'">
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
                            <div class="panel panel-default col-xs-5 col-md-2" ng-repeat="(header_align_name,header_align_value) in extra.theme_skins[settings.theme_skin].params.options.option_article_header_align">
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


                      <div class="col-xs-12 col-md-4 m-t-15">
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
                              <option value="[% share_tools_name %]" ng-repeat="(share_tools_name,share_tools_value) in extra.theme_skins[settings.theme_skin].params.options.option_share_tools" ng-selected="[% share_tools_name === settings.theme_options.share_tools || settings.theme_options.share_tools == undefined %]">[% share_tools_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-12 col-md-4 m-t-15">
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
                              <option value="[% tags_display_name %]" ng-repeat="(tags_display_name,tags_display_value) in extra.theme_skins[settings.theme_skin].params.options.option_tags_display" ng-selected="[% tags_display_name === settings.theme_options.tags_display || settings.theme_options.tags_display == undefined %]">[% tags_display_value %]</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="col-xs-12 col-md-4 m-t-15">
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
                              <option value="[% related_contents_name %]" ng-repeat="(related_contents_name,related_contents_value) in extra.theme_skins[settings.theme_skin].params.options.option_related_contents" ng-selected="[% related_contents_name === settings.theme_options.related_contents || settings.theme_options.related_contents == undefined %]">[% related_contents_value %]</option>
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
