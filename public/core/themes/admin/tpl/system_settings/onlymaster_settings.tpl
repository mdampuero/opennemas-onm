{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="MasterSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-rebel fa-lg"></i>
                {t}Only masters{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <div class="form-group">
                  <label class="my-switch">
                    <input type="checkbox" id="themeSwitch" ng-model="isDarkTheme" ng-change="toggleAllEditorsTheme()">
                    <span class="my-slider round"></span>
                  </label>
                </div>
              </li>
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
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
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
                    </div>
                    <h4>
                      <i class="fa fa-newspaper-o"></i>
                      {t}Maximum elements per frontpage{/t}
                    </h4>
                    <div class="controls">
                      <input class="form-control" id="frontpage_max_items" name="frontpage_max_items" ng-model="settings.frontpage_max_items" type="number" min="10" placeholder="100">
                    </div>
                    <h4>
                      <i class="fa fa-android"></i>
                      Robots.txt
                    </h4>
                    <div class="form-group">
                      <label class="form-label" for="robots-txt-rules">
                        {t}Robots.txt rules{/t}
                      </label>
                      <span class="help">
                        {t escape=off}Add custom robots.txt rules like 'Disallow: /tag'. Refer to the <a href="http://www.robotstxt.org/robotstxt.html" target="_blank" ng-click="$event.stopPropagation();">documentation</a>.{/t}
                      </span>
                      <div class="controls">
                        <textarea class="form-control" id="robots-txt-rules" name="robots-txt-rules" ng-model="settings.robots_txt_rules" rows="6"></textarea>
                      </div>
                    </div>
                    <div ng-if="extra.theme_skins[settings.theme_skin].params.fonts">
                      <div class="row">
                        <div class="col-xs-12 m-b-15">
                          <h4>
                            <i class="fa fa-font"></i>
                            {t}Theme fonts{/t}
                          </h4>
                        </div>
                        <div class="col-xs-12 col-md-6 m-b-15">
                          <label class="form-label" for="theme-font">
                            <span class="help">
                              {t}Select your main font (titles, headings…).{/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-font" name="theme-font" ng-model="settings.theme_font">
                                <option value="[% font_name %]" ng-repeat="(font_name,font_url) in extra.theme_skins[settings.theme_skin].params.fonts" ng-selected="[% font_name === settings.theme_font %]">[% font_url %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-xs-12 col-md-6 m-b-15">
                          <label class="form-label" for="theme-font-secondary">
                            <span class="help">
                              {t}Secondary font (body, summary…){/t}
                            </span>
                          </label>
                          <div class="controls">
                            <div class="input-group">
                              <select id="theme-font-secondary" name="theme-font-secondary" ng-model="settings.theme_font_secondary">
                                <option value="[% secondary_font_name %]" ng-repeat="(secondary_font_name,secondary_font_url) in extra.theme_skins[settings.theme_skin].params.fonts" ng-selected="[% secondary_font_name === settings.theme_font_secondary || settings.theme_font_secondary == undefined %]">[% secondary_font_url %]</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-retweet"></i>
                      {t}Redirection{/t}
                    </h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input {if $configs['redirection'] eq "1"}checked{/if} id="redirection" name="redirection" ng-false-value="0" ng-model="settings.redirection" ng-true-value="'1'" type="checkbox">
                        <label for="redirection">
                          {t}Redirect to frontpage non-migrated contents{/t}
                        </label>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-google"></i>
                      {t}Google Analytics{/t}
                    </h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="disable_dga" name="disable_dga" ng-false-value="'0'" ng-model="settings.disable_default_ga" ng-true-value="'1'" type="checkbox"/>
                        <label for="disable_dga">
                          {t}Disable Opennemas Google Analytics API key{/t}
                        </label>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-signal"></i>
                      {t}GFK{/t}
                    </h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="gfk_only_amp" name="gfk_only_amp" ng-false-value="'0'" ng-model="settings.gfk.only_amp" ng-true-value="'1'" type="checkbox"/>
                        <label for="gfk_only_amp">
                          {t}Only AMP{/t}
                        </label>
                        <input id="gfk_pre_mode" name="gfk_pre_mode" ng-false-value="'0'" ng-model="settings.gfk.pre_mode" ng-true-value="'1'" type="checkbox"/>
                        <label for="gfk_pre_mode">
                          {t}Preproduction{/t}
                        </label>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-rss-square"></i>
                      {t}RSS{/t}
                    </h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input {if $configs['full_rss'] eq "1"}checked{/if} id="full_rss" name="full_rss" ng-false-value="0" ng-model="settings.full_rss" ng-true-value="'1'" type="checkbox">
                        <label for="full_rss">
                          {t}Show full content on RSS{/t}
                        </label>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-stop-circle"></i>
                      {t}Lazyscript referer detect{/t}
                    </h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input {if $configs['lazy_referer'] eq "1"}checked{/if} id="lazy_referer" name="lazy_referer" ng-false-value="0" ng-model="settings.lazy_referer" ng-true-value="'1'" type="checkbox">
                        <label for="lazy_referer">
                          {t}Disabled{/t}
                        </label>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-search"></i>
                      {t}SEO Information{/t}
                    </h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input id="seo_information" name="seo_information" ng-false-value="0" ng-model="settings.seo_information" ng-true-value="1" type="checkbox">
                        <label for="seo_information">
                          {t}Show SEO information on contents{/t}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-code"></i>
                      {t}Scripts{/t} Web
                    </h4>
                    <div class="form-group">
                      <label class="form-label" for="header-script">
                        {t}Scripts in header{/t}
                        <span class="help">{t}This scripts will be included before the </head> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="header-script" name="header-script" ng-model="settings.header_script" rows="6"></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="body-start-script">
                        {t}Scripts at body start{/t}
                        <span class="help">{t}This scripts will be included before the <body> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="body-start-script" name="body-start-script" ng-model="settings.body_start_script" rows="6"></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="body-end-script">
                        {t}Scripts at body end{/t}
                        <span class="help">{t}This scripts will be included before the </body> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="body-end-script" name="body-end-script" ng-model="settings.body_end_script" rows="6"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-code"></i>
                      {t}Scripts{/t} AMP
                    </h4>
                    <div class="form-group">
                      <label class="form-label" for="header-script-amp">
                        {t}Scripts in header{/t}
                        <span class="help">{t}This scripts will be included before the </head> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="header-script-amp" name="header-script-amp" ng-model="settings.header_script_amp" rows="6"></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="body-start-script-amp">
                        {t}Scripts at body start{/t}
                        <span class="help">{t}This scripts will be included before the <body> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="body-start-script-amp" name="body-start-script-amp" ng-model="settings.body_start_script_amp" rows="6"></textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="body-end-script-amp">
                        {t}Scripts at body end{/t}
                        <span class="help">{t}This scripts will be included before the </body> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="body-end-script-amp" name="body-end-script-amp" ng-model="settings.body_end_script_amp" rows="6"></textarea>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-code"></i>
                      {t}CSS{/t} AMP
                    </h4>
                    <div class="form-group">
                      <label class="form-label" for="custom-css-amp">
                        <span class="help">{t}This css will be added at the end of the original{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="custom-css-amp" name="custom-css-amp" ng-model="settings.custom_css_amp" rows="6"></textarea>
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
