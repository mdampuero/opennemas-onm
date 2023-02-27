
{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="AppearanceSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-magic fa-lg"></i>
                {t}Settings{/t} > {t}Appearance{/t}
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
          <div class="row">
            <div class="col-md-6">
              <div class="p-r-15">
                <div class="row">
                  <div class="col-md-12">
                    <h4>
                      <i class="fa fa-paint-brush"></i>
                      {t}Colors{/t}
                    </h4>
                    <div class="form-group col-md-10">
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
                    <div class="form-group col-md-10">
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
                  <div class="col-md-12">
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
                  <div class="form-group col-md-12" ng-show="settings.logo_enabled">
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
                  <div class="form-group col-md-12" ng-if="settings.logo_enabled">
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
                  <div class="form-group col-md-12" ng-if="settings.logo_enabled">
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
                  <div class="form-group col-md-12" ng-if="settings.logo_enabled">
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
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-l-15">
                <h4>
                  <i class="fa fa-eye"></i>
                  {t}Cookies agreement{/t}
                </h4>
                <div class="controls">
                  <div class="radio">
                    <input class="form-control" id="cookies-none" ng-model="settings.cookies" ng-value="'none'" type="radio"/>
                    <label for="cookies-none">
                      {t}None{/t}
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="cookies-default" ng-model="settings.cookies" ng-value="'default'" type="radio"/>
                    <label for="cookies-default">
                      {t}Basic cookies advise{/t}
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="cookies-cmp-default" ng-model="settings.cookies" ng-value="'cmp'" type="radio"/>
                    <label for="cookies-cmp-default">
                      {t}Consent Management Platform (CMP){/t}
                    </label>
                  </div>
                </div>
                <div class="form-group" ng-if="settings.cookies == 'default'">
                  <label class="form-label" for="cookies-hint-url">
                    {t}Cookie agreement page URL{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="cookies-hint-url" name="cookies-hint-url" ng-model="settings.cookies_hint_url" type="text">
                  </div>
                </div>
                <div class="form-group m-t-15" ng-if="settings.cookies == 'cmp'">
                  <label class="form-label" for="cmp-type">
                    {t}Choose your CMP{/t}
                  </label>
                  <div class="controls">
                    <div class="radio">
                      <input class="form-control" id="cmp-default" ng-model="settings.cmp_type" ng-value="'default'" type="radio"/>
                      <label for="cmp-default">
                        {t}Default{/t}
                      </label>
                    </div>
                    <div class="radio">
                      <input class="form-control" id="cmp-quantcast" ng-model="settings.cmp_type" ng-value="'quantcast'" type="radio"/>
                      <label for="cmp-quantcast">
                        {t}Quantcast{/t}
                      </label>
                    </div>
                    <div class="radio">
                      <input class="form-control" id="cmp-onetrust" ng-model="settings.cmp_type" ng-value="'onetrust'" type="radio"/>
                      <label for="cmp-onetrust">
                        {t}OneTrust{/t}
                      </label>
                    </div>
                  </div>
                  <div class="form-group m-t-15" ng-if="settings.cmp_type == 'quantcast'">
                    <label class="form-label" for="cmp-id">
                      {t}Quantcast UTID{/t}
                    </label>
                    <span class="help">
                      {t escape=off}How to find your Quantcast UTID <a class="external-link" href="https://help.quantcast.com/hc/en-us/articles/360051794614-TCF-v2-GTM-Implementation-Guide-Finding-your-UTID" target="_blank" ng-click="$event.stopPropagation();">here</a>.{/t}
                    </span>
                    <div class="controls">
                      <input class="form-control" id="cmp-id" name="cmp-id" ng-model="settings.cmp_id" type="text">
                    </div>
                  </div>
                  <div class="form-group m-t-15" ng-if="settings.cmp_type == 'onetrust'">
                    <label class="form-label" for="cmp-id">
                      {t}OneTrust data-domain-script{/t}
                    </label>
                    <span class="help">
                      {t escape=off}Get your data-domain-script from your OneTrust script. Check an example script <a class="external-link" href="https://community.cookiepro.com/s/article/UUID-5394213a-70b9-c4e6-d68c-f809b55e7af6#UUID-7478d3b4-18eb-3ac0-a6fd-fb7ebff9f8dc_section-idm4591571479548831554522590036" target="_blank" ng-click="$event.stopPropagation();">here</a>.{/t}
                    </span>
                    <div class="controls">
                      <input class="form-control" id="cmp-id" name="cmp-id" ng-model="settings.cmp_id" type="text">
                    </div>
                  </div>
                  <div class="form-group m-t-15" ng-if="settings.cmp_type == 'onetrust' || settings.cmp_type == 'quantcast'">
                    <div class="checkbox">
                      <input class="form-control" id="cmp-amp" name="cmp-amp" ng-false-value="'0'" ng-true-value="'1'" ng-model="settings.cmp_amp" type="checkbox"/>
                      <label class="form-label" for="cmp-amp">
                        {t}Use CMP on your AMP pages{/t}
                      </label>
                      <span class="badge badge-default text-bold text-uppercase">
                        Beta
                      </span>
                    </div>
                  </div>
                </div>
                <h4>
                  <i class="fa fa-internet-explorer"></i>
                  {t}Browser update{/t}
                </h4>
                <div class="form-group">
                  <div class="checkbox">
                    <input class="form-control" id="browser-update" name="browser-update" ng-false-value="'0'" ng-model="settings.browser_update" ng-true-value="'1'"  type="checkbox"/>
                    <label class="form-label" for="browser-update">
                      {t}Notify users that they should update their browser{/t}
                    </label>
                  </div>
                </div>
                <h4>
                  <i class="fa fa-list"></i>
                  {t}Listing{/t}
                </h4>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label class="form-label" for="items-per-page">
                      {t}Items per page{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="items-per-page" name="items-per-page" ng-model="settings.items_per_page" type="number">
                    </div>
                  </div>
                  {is_module_activated name="FRONTPAGES_LAYOUT"}
                  <div class="col-md-6 form-group">
                    <label class="form-label" for="items-in-blog">
                      {t}Items per blog page{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="items-in-blog" name="items-in-blog" ng-model="settings.items_in_blog" type="number">
                    </div>
                  </div>
                  {/is_module_activated}
                </div>
                {if $app.security->hasPermission('MASTER')}
                <h4>
                  <i class="fa fa-rss"></i>
                  RSS
                </h4>
                <div class="form-group">
                  <label class="form-label" for="elements-in-rss">
                    {t}Items in RSS{/t}
                  </label>
                  <div class="controls">
                    <input class="form-control" id="elements-in-rss" name="elements-in-rss" ng-model="settings.elements_in_rss" type="number">
                  </div>
                </div>
                {/if}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}

