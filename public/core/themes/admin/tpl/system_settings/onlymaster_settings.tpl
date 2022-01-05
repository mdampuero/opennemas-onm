{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="SettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-rebel fa-lg"></i>
                {t}Settings{/t} > {t}General{/t} > {t}Only masters{/t}
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
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
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
                  </div>
                  <div class="form-group" ng-if="extra.theme_skins.length !== 0">
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
                    <div class="form-group">
                      <label class="form-label" for="frontpage_max_items">
                        {t}Elements per frontpage{/t}
                      </label>
                      <span class="help ">
                          (min. 10)
                      </span>
                      <div class="controls">
                        <input class="form-control" id="frontpage_max_items" name="frontpage_max_items" ng-model="settings.frontpage_max_items" type="number" min="10">
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
