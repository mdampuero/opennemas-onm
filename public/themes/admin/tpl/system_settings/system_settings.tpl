{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('#allow_logo').on('click', function(){
          if($(this).is(':checked')) {
            $('#site_logo_block').show();
          } else {
            $('#site_logo_block').hide();
          }
        });

        $('.check-pass').on('click', function(e){
          e.preventDefault();
          var passInput = $('#onm_digest_pass');
          var btn = $(this);
          if (passInput.attr('type') == 'password') {
            passInput.prop('type','text');
          } else {
            passInput.prop('type','password');
          }

          btn.find('i').toggleClass('fa-unlock-alt');
        });

        $('.external-link').on('click', function(e) {
          e.stopPropagation();
        });

        // Logo, mobile and favico image uploader
        $('.fileinput.site-logo').fileinput({ name: 'site_logo', uploadtype:'image' });
        $('.fileinput.favico').fileinput({ name: 'favico', uploadtype:'image' });
        $('.fileinput.mobile-logo').fileinput({ name: 'mobile_logo', uploadtype:'image' });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form ng-app="BackendApp" ng-controller="SystemSettingsCtrl" action="{url name="admin_system_settings_save"}" enctype="multipart/form-data" method="POST" id="formulario">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-cogs fa-lg"></i>
                {t}Settings{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
                  <i class="fa fa-save"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple settings">
        <div class="grid-body no-padding ng-cloak">
          <uib-tabset>
            <uib-tab heading="{t}General{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-8">
                    <div class="form-group">
                      <label class="form-label" for="site_name">
                        {t}Site name{/t}
                      </label>
                      <span class="help">
                        {t}This will be displayed as your site name.{/t}
                      </span>
                      <div class="controls">
                        <input class="form-control" id="site_name" name="site_name" required="required" type="text" value="{$configs['site_name']|default:""}" class="input-xlarge">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_agency">
                        {t}Site agency{/t}
                      </label>
                      <span class="help">
                        {t}This will be displayed as the default article signature.{/t}
                      </span>
                      <div class="controls">
                        <input class="form-control"  id="site_agency" name="site_agency" type="text" value="{$configs['site_agency']|default:""}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_footer">
                        {t}Footer text{/t}
                      </label>
                      <span class="help">
                        {t}Text showed at the bottom of your page. Usually used for copyright notice.{/t}
                      </span>
                      <div class="controls">
                        <textarea class="form-control" onm-editor onm-editor-preset="simple" id="site_footer" ng-model="site_footer" name="site_footer">{$configs['site_footer']|default:""}</textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab heading="{t}Appearance{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-8">
                    <div class="form-group">
                      <label class="form-label" for="site_color">
                        {t}Site color{/t}
                      </label>
                      <span class="help">
                        {t}Color used for links, menus and some widgets.{/t}
                      </span>
                      <div class="controls">
                        <div class="input-group">
                          <span class="input-group-addon" ng-if="site_color.indexOf('#') > -1" ng-style="{ 'background-color': site_color }">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                          </span>
                          <span class="input-group-addon" ng-if="site_color.indexOf('#') <= -1" ng-style="{ 'background-color': '#' + site_color }">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                          </span>
                          <input class="form-control" id="site_color" name="site_color" colorpicker="hex" ng-model="site_color" type="text" ng-init="site_color='{$configs['site_color']|default:""}'">
                          <div class="input-group-btn">
                            <button class="btn btn-default" ng-click="site_color='{$configs['site_color']|default:""}'" type="button">{t}Reset{/t}</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_color_secondary">
                        {t}Site secondary color{/t}
                      </label>
                      <span class="help">
                        {t}Color used for custom elements.{/t}
                      </span>
                      <div class="controls">
                        <div class="input-group">
                          <span class="input-group-addon" ng-if="site_color_secondary.indexOf('#') > -1" ng-style="{ 'background-color': site_color_secondary }">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                          </span>
                          <span class="input-group-addon" ng-if="site_color_secondary.indexOf('#') <= -1" ng-style="{ 'background-color': '#' + site_color_secondary }">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                          </span>
                          <input class="form-control" id="site_color_secondary" name="site_color_secondary" colorpicker="hex" ng-model="site_color_secondary" type="text" ng-init="site_color_secondary='{$configs['site_color_secondary']|default:""}'">
                          <div class="input-group-btn">
                            <button class="btn btn-default" ng-click="site_color_secondary='{$configs['site_color_secondary']|default:""}'" type="button">{t}Reset{/t}</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="checkbox">
                        <input class="form-control" id="allow_logo" name="section_settings[allowLogo]" type="checkbox" value="1" {if $configs['section_settings']['allowLogo'] eq "1"}checked{/if}/>
                        <label class="form-label" for="allow_logo">
                          {t}Use custom logo{/t}
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group" id="site_logo_block" {if $configs['section_settings']['allowLogo'] eq 0}style="display:none"{/if}>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="form-label" for="site_logo">{t}Large logo{/t}</label>
                          <div class="controls">
                            <div class="fileinput site-logo {if !empty($configs['site_logo'])}fileinput-exists{else}fileinput-new{/if}" data-provides="fileinput">
                              <div class="fileinput-exists fileinput-preview thumbnail" style="max-width: 200px; max-height: 150px;">
                                {if !empty($configs['site_logo'])}
                                  <img src="{$smarty.const.MEDIA_URL}{$smarty.const.MEDIA_DIR}/sections/{$configs['site_logo']}" alt="{t}Site logo{/t}"/>
                                {/if}
                              </div>
                              <div>
                                <span class="btn btn-file">
                                  <span class="fileinput-new">{t}Pick image{/t}</span>
                                  <span class="fileinput-exists">{t}Change{/t}</span>
                                  <input type="file"/>
                                  <input type="hidden" class="file-input">
                                </span>
                                <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                                  <i class="fa fa-trash-o"></i>
                                  {t}Remove{/t}
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="form-label" for="mobile_logo">{t}Small logo{/t}</label>
                          <div class="controls">
                            <div class="fileinput mobile-logo {if !empty($configs['mobile_logo'])}fileinput-exists{else}fileinput-new{/if}" data-provides="fileinput">
                              <div class="fileinput-exists fileinput-preview thumbnail" style="max-width: 100px; max-height: 60px;">
                                {if !empty($configs['mobile_logo'])}
                                  <img src="{$smarty.const.MEDIA_URL}{$smarty.const.MEDIA_DIR}/sections/{$configs['mobile_logo']}" alt="{t}Site logo{/t}"/>
                                {/if}
                              </div>
                              <div>
                                <span class="btn btn-file">
                                  <span class="fileinput-new">{t}Pick image{/t}</span>
                                  <span class="fileinput-exists">{t}Change{/t}</span>
                                  <input type="file"/>
                                  <input type="hidden" class="file-input">
                                </span>
                                <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                                  <i class="fa fa-trash-o"></i>
                                  {t}Remove{/t}
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="form-label" for="favico">{t}Favico{/t}</label>
                          <div class="controls">
                            <div class="fileinput favico {if !empty($configs['favico'])}fileinput-exists{else}fileinput-new{/if}" data-provides="fileinput">
                              <div class="fileinput-exists fileinput-preview thumbnail" style="max-width: 35px; max-height: 35px;">
                                {if !empty($configs['favico'])}
                                  <img src="{$smarty.const.MEDIA_URL}{$smarty.const.MEDIA_DIR}/sections/{$configs['favico']}" alt="{t}Site logo{/t}"/>
                                {/if}
                              </div>
                              <div>
                                <span class="btn btn-file">
                                  <span class="fileinput-new">{t}Pick image{/t}</span>
                                  <span class="fileinput-exists">{t}Change{/t}</span>
                                  <input type="file"/>
                                  <input type="hidden" class="file-input">
                                </span>
                                <a href="#" class="btn btn-danger fileinput-exists delete" data-dismiss="fileinput">
                                  <i class="fa fa-trash-o"></i>
                                  {t}Remove{/t}
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab heading="{t}SEO{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <h4>{t}SEO options{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="site_title">
                        {t}Site title{/t}
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="site_title" name="site_title" rows="5">{$configs['site_title']|default:""}</textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_description">
                        {t}Site description{/t}
                      </label>
                      <div class="controls">
                      <textarea class="form-control" id="site_description" name="site_description" rows="5">{$configs['site_description']|default:""}</textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_description">
                       {t}Site keywords{/t}
                     </label>
                     <div class="controls">
                      <textarea class="form-control" id="site_keywords" name="site_keywords" rows="5">{$configs['site_keywords']|default:""}</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h4>{t}Web Master Tools{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="webmastertools_google">
                        {t}Google Web Master Tools{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="webmastertools_google" name="webmastertools_google" type="text" value="{$configs['webmastertools_google']|default:""}">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="webmastertools_bing">
                        {t}Bing Web Master Tools{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control"  id="webmastertools_bing" name="webmastertools_bing" class="input-xlarge" type="text" value="{$configs['webmastertools_bing']|default:""}">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab heading="{t}Internal{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <h4>{t}Cookies agreement{/t}</h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input class="form-control" id="cookies_hint_enabled" name="cookies_hint_enabled" type="checkbox" value="1" {if $configs['cookies_hint_enabled'] == 1}checked{/if}>
                        <label class="form-label" for="cookies_hint_enabled">
                          {t}Enable cookies agreement{/t}
                        </label>
                      </div>
                      <div class="controls">
                        <span class="help">
                          {t}Mark this if you want to show a message to your users that your site is using cookies.{/t}
                        </span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="webmastertools_bing">
                        {t}Cookie agreement page URL{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="cookies_hint_url" name="cookies_hint_url" type="text" value="{$configs['cookies_hint_url']|default:""}">
                      </div>
                    </div>
                    <h4>{t}Language & Time{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="country">{t}Country{/t}</label>
                      <div class="controls">
                        <select id="country" name="country">
                          <option value="">{t}Select a country{/t}...</option>
                          {foreach from=$countries key=key item=value}
                            <option{if $country === $key} selected="selected"{/if} value="{{$key}}">{{$value}}</option>
                          {/foreach}
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="site_language">
                        {t}Default language{/t}
                      </label>
                      <span class="help">
                        {t}Used for displayed messages, interface and measures in your page.{/t}
                      </span>
                      <div class="controls">
                        {html_options name=site_language options=$languages selected=$configs['site_language']}
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="time_zone">
                        {t}Time Zone{/t}
                      </label>
                      <span class="help">
                        {t}Used for all the dates used in your webpage.{/t}
                      </span>
                      <div class="controls">
                        <select name="time_zone">
                          {foreach from=$timezones item=name key=id}
                            <option value="{{$name}}" {if $configs['time_zone'] == $id || $configs['time_zone'] == $name}selected="selected"{/if}>{{$name}}</option>
                          {/foreach}
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h4>{t}Listing & sorting{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="refresh_interval">
                        {t}Refresh page interval{/t}
                        <small>(seconds)</small>
                      </label>
                      <span class="help">
                        {t}When a user visits pages and stay on it for a while, this setting allows to refresh the loaded page for updated it.{/t}
                      </span>
                      <div class="controls">
                        <input class="form-control" id="refresh_interval" name="refresh_interval" type="number" value="{$configs['refresh_interval']|default:900}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label class="form-label" for="items_per_page">
                          {t}Items per page{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="items_per_page" name="items_per_page" type="number" value="{$configs['items_per_page']|default:20}">
                        </div>
                      </div>
                      {is_module_activated name="FRONTPAGES_LAYOUT"}
                      <div class="col-md-6 form-group">
                        <label class="form-label" for="items_in_blog">
                          {t}Items per blog page{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="items_in_blog" name="items_in_blog" type="number" value="{$configs['items_in_blog']|default:10}">
                        </div>
                      </div>
                      {/is_module_activated}
                    </div>
                    {is_module_activated name="FORM_MANAGER"}
                    <h4>{t}Form Module{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="contact_email">
                        {t}Contact email{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="contact_email" name="contact_email" type="text" value="{$configs['contact_email']}">
                      </div>
                    </div>
                    {/is_module_activated}
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab heading="{t}External services{/t}">
              <div class="tab-wrapper">
                <div class="col-md-6">
                  <h5>{t}Analytic system integration{/t}</h5>
                  <div class="panel-group" id="accordion_google_analytics" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_google_analytics" href="#goggle">
                            <i class="fa fa-google"></i>{t}Google Analytics{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="goggle" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div ng-init="init({json_encode($configs['google_analytics'])|clear_json})">
                             <div class="row" ng-if="gaCodes.length <= 1">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label">
                                    {t}Google Analytics API key{/t}
                                  </label>
                                  <div class="controls">
                                    <input class="form-control" name="google_analytics[0][api_key]" type="text"  ng-model="gaCodes[0].api_key" value="[% gaCodes[0].api_key %]">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label">
                                    {t}Google Analytics Base domain{/t}
                                  </label>
                                  <div class="controls">
                                    <input class="form-control" name="google_analytics[0][base_domain]" type="text"  ng-model="gaCodes[0].base_domain" value="[% gaCodes[0].base_domain %]">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6" ng-show="{if $smarty.session._sf2_attributes.user->isMaster()}true{/if}">
                                <div class="form-group">
                                  <label class="form-label">
                                    {t}Category targeting{/t}
                                  </label>
                                  <div class="row">
                                    <div class="col-md-4">
                                      <div class="help">
                                        {t}Index{/t}
                                      </div>
                                      <div class="controls">
                                        <input class="form-control" name="google_analytics[0][category][idx]" type="text" ng-model="gaCodes[0].category.idx" ng-value="[% gaCodes[0].category[idx] %]">
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="help">
                                        {t}Key{/t}
                                      </div>
                                      <div class="controls">
                                        <input class="form-control" name="google_analytics[0][category][key]" type="text" ng-model="gaCodes[0].category.key" ng-value="[% gaCodes[0].category[key] %]">
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="help">
                                        {t}Scope{/t}
                                      </div>
                                      <div class="controls">
                                        <input class="form-control" name="google_analytics[0][category][scp]" type="text" ng-model="gaCodes[0].category.scp" ng-value="[% gaCodes[0].category[scp] %]">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6" ng-show="{if $smarty.session._sf2_attributes.user->isMaster()}true{/if}">
                                <div class="form-group">
                                  <label class="form-label">
                                    {t}Module targeting{/t}
                                  </label>
                                  <div class="row">
                                    <div class="col-md-4">
                                      <div class="help">
                                        {t}Index{/t}
                                      </div>
                                      <div class="controls">
                                        <input class="form-control" name="google_analytics[0][module][idx]" type="text" ng-model="gaCodes[0].module.idx" ng-value="[% gaCodes[0].module[idx] %]">
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="help">
                                        {t}Key{/t}
                                      </div>
                                      <div class="controls">
                                        <input class="form-control" name="google_analytics[0][module][key]" type="text" ng-model="gaCodes[0].module.key" ng-value="[% gaCodes[0].module[key] %]">
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="help">
                                        {t}Scope{/t}
                                      </div>
                                      <div class="controls">
                                        <input class="form-control" name="google_analytics[0][module][scp]" type="text" ng-model="gaCodes[0].module.scp" ng-value="[% gaCodes[0].module[scp] %]">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12" ng-show="{if $smarty.session._sf2_attributes.user->isMaster()}true{/if}">
                                <div class="form-group">
                                  <label class="form-label">
                                    {t}Google Analytics Custom variables{/t}
                                  </label>
                                  <div class="controls">
                                      <textarea class="form-control" name="google_analytics[0][custom_var]" type="text" class="input-xlarge" ng-model="gaCodes[0].custom_var" value="[% gaCodes[0].custom_var %]"></textarea>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group other-analytics" ng-if="gaCodes.length > 1" ng-repeat="code in gaCodes track by $index">
                              <div class="row" ng-model="gaCodes[$index]">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Google Analytics API key{/t}
                                    </label>
                                    <div class="controls">
                                      <input class="form-control" name="google_analytics[[% $index %]][api_key]" type="text" ng-model="code.api_key" ng-value="[% code.api_key %]" required>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Google Analytics Base domain{/t}
                                    </label>
                                    <div class="controls">
                                      <div class="input-group">
                                        <input class="form-control" name="google_analytics[[% $index %]][base_domain]" type="text" ng-model="code.base_domain" ng-value="[% code.base_domain %]">
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger" ng-click="removeGanalytics(gaCodes, $index)" type="button">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6" ng-show="{if $smarty.session._sf2_attributes.user->isMaster()}true{/if}">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Category targeting{/t}
                                    </label>
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Index{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[[% $index %]][category][idx]" type="text" ng-model="code.category.idx" ng-value="[% code.category.idx %]">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Key{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[[% $index %]][category][key]" type="text" ng-model="code.category.key" ng-value="[% code.category.key %]">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Scope{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[[% $index %]][category][scp]" type="text" ng-model="code.category.scp" ng-value="[% code.category.scp %]">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6" ng-show="{if $smarty.session._sf2_attributes.user->isMaster()}true{/if}">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Module targeting{/t}
                                    </label>
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Index{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[[% $index %]][module][idx]" type="text" ng-model="code.module.idx" ng-value="[% code.module.idx %]">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Key{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[[% $index %]][module][key]" type="text" ng-model="code.module.key" ng-value="[% code.module.key %]">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Scope{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[[% $index %]][module][scp]" type="text" ng-model="code.module.scp" ng-value="[% code.module.scp %]">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12" ng-show="{if $smarty.session._sf2_attributes.user->isMaster()}true{/if}">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Google Analytics Custom variables{/t}
                                    </label>
                                    <div class="controls">
                                        <textarea class="form-control" name="google_analytics[[% $index %]][custom_var]" type="text" class="input-xlarge" ng-model="code.custom_var" value="[% code.custom_var %]"></textarea>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group" ng-if="gaCodes[0].api_key">
                              <div class="input-group">
                                <div class="input-group-btn">
                                  <button class="btn btn-default" ng-click="addGanalytics();" type="button">{t}Add another{/t}</button>
                                </div>
                              </div>
                            </div>
                            <p>{t escape=off}You can get your Google Analytics Site ID from <a class="external-link" href="https://www.google.com/analytics/" target="_blank">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3).{/t}</p>
                            <p><i class="fa fa-info-circle"></i> {t}We are not responsible of the stats or of any third party services{/t}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_comscore" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_comscore" href="#comscore">
                            <i class="fa fa-area-chart"></i>{t}ComScore Statistics{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="comscore" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="comscore_page_id">
                              {t}comScore Page ID{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="comscore_page_id" name="comscore[page_id]" type="text" value="{$configs['comscore']['page_id']|default:""}">
                              <div class="help">
                                {t escape=off}If you also have a <strong>comScore statistics service</strong>, add your page id{/t}
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <i class="fa fa-info-circle"></i> {t}We are not responsible of the stats or of any third party services{/t}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_ojd" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_ojd" href="#ojd">
                            <i class="fa fa-line-chart"></i>{t}OJD Statistics{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="ojd" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="ojd_page_id">
                              {t}OJD Page ID{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="ojd_page_id" name="ojd[page_id]" type="text" value="{$configs['ojd']['page_id']|default:""}">
                              <div class="help">{t escape=off}If you also have a <strong>OJD statistics service</strong>, add your page id{/t}</div>
                            </div>
                          </div>
                          <div class="form-group">
                            <i class="fa fa-info-circle"></i> {t}We are not responsible of the stats or of any third party services{/t}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_chartbeat" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_chartbeat" href="#chartbeat">
                            <i class="fa fa-bar-chart"></i>{t}Chartbeat{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="chartbeat" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="chartbeat_id">
                              {t}Chartbeat Account ID{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="chartbeat_id" name="chartbeat[id]" type="text" value="{$configs['chartbeat']['id']|default:""}">
                              <div class="help">{t escape=off}If you also have a <strong>Charbeat statistics service</strong>, add your account id{/t}</div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="chartbeat_domain">
                              {t}Chartbeat Domain{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="chartbeat_domain" name="chartbeat[domain]" type="text" value="{$configs['chartbeat']['domain']|default:""}">
                            </div>
                          </div>
                          <div class="form-group">
                            <i class="fa fa-info-circle"></i> {t}We are not responsible of the stats or of any third party services{/t}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <h5>{t}Internal settings{/t}</h5>
                  <div class="panel-group" id="accordion_recaptcha" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_recaptcha" href="#recaptcha">
                            <i class="fa fa-keyboard-o"></i>{t}Recaptcha{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="recaptcha" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="piwik_token_auth">
                              {t}Public key{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="recaptcha_public_key" name="recaptcha[public_key]" type="text" value="{$configs['recaptcha']['public_key']|default:""}">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="piwik_token_auth">
                              {t}Private key{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="recaptcha_private_key" name="recaptcha[private_key]" type="text" value="{$configs['recaptcha']['private_key']|default:""}">
                            </div>
                          </div>
                          <span class="help">
                            {t escape=off}Get your reCaptcha key from <a class="external-link" href="https://www.google.com/recaptcha/admin#whyrecaptcha" target="_blank">this page</a>.{/t} {t}Used when we want to test if the user is an human and not a robot.{/t}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_onmagency" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_onmagency" href="#news_agency">
                            <i class="fa fa-microphone"></i>{t}Opennemas News Agency{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="news_agency" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-6">
                                <label class="form-label" for="onm_digest_user">
                                  {t}User{/t}
                                </label>
                                <div class="controls">
                                  <input class="form-control" id="onm_digest_user" name="onm_digest_user" type="text" value="{$configs['onm_digest_user']|default:""}">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <label class="form-label" for="onm_digest_pass">
                                  {t}Password{/t}
                                </label>
                                <div class="controls">
                                  <div class="input-group">
                                    <input class="form-control" id="onm_digest_pass" name="onm_digest_pass" type="password" value="{$configs['onm_digest_pass']|default:""}">
                                    <div class="input-group-btn">
                                      <button class="btn check-pass" type="button">
                                        <i class="fa fa-lock"></i>
                                      </button>
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
                  {is_module_activated name="PAYWALL"}
                  <div class="panel-group" id="accordion_paypal" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_paypal" href="#paypal">
                            <i class="fa fa-paypal"></i>{t}Paypal Settings{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="paypal" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="paypal_mail">
                              {t}Account email:{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="paypal_mail" name="paypal_mail" type="text" value="{$configs['paypal_mail']|default:""}">
                              <div class="help">
                                {t escape=off}You can get your PayPal account email from <a class="external-link" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_registration-run" target="_blank">PayPal site</a>. This must be a business account for receiving payments{/t}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  {/is_module_activated}
                  <div class="panel-group" id="accordion_google" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_google" href="#goggle-services">
                            <i class="fa fa-google"></i>{t}Google Services{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="goggle-services" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="google_custom_search_api_key">
                              {t}Google Search API key:{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="google_custom_search_api_key" name="google_custom_search_api_key" type="text" value="{$configs['google_custom_search_api_key']|default:""}">
                              <span class="help">
                                {t escape=off}You can get your Google <strong>Search</strong> API Key from <a class="external-link" href="http://www.google.com/cse/manage/create" target="_blank">Google Search sign up website</a>.{/t}
                              </span>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="google_news_name">
                              {t}Publication name in Google News{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="google_news_name" name="google_news_name" type="text" value="{$configs['google_news_name']|default:""}">
                              <span class="help">
                                {t escape=off}You can get your Publication name in <a class="external-link" href="https://www.google.es/search?num=100&hl=es&safe=off&gl=es&tbm=nws&q={$smarty.server.HTTP_HOST}&oq={$smarty.server.HTTP_HOST}" target="_blank">Google News search</a> for your site.{/t}
                              </span>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="google_maps_api_key">
                              {t}Google Maps API key{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="google_maps_api_key" name="google_maps_api_key" type="text" value="{$configs['google_maps_api_key']|default:""}">
                              <span class="help">
                                {t escape=off}You can get your Google <strong>Maps</strong> API Key from <a class="external-link" href="http://code.google.com/apis/maps/signup.html" target="_blank">Google maps sign up website</a>.{/t}
                              </span>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="google_tags_id">
                              {t}Google Tags container Id{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="google_tags_id" name="google_tags_id" type="text" value="{$configs['google_tags_id']|default:""}">
                              <span class="help">
                                {t escape=off}You can get your Google <strong>Tags</strong> container Id from <a class="external-link" href="https://tagmanager.google.com/#/home" target="_blank">Google tags sign up website</a>.{/t}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <h5>{t}Social network integration{/t}</h5>
                  <div class="panel-group" id="accordion_socialnetwork" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_socialnetwork" href="#goggle-plus">
                            <i class="fa fa-youtube"></i>{t}Google+ and YouTube{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="goggle-plus" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="google_page">
                              {t}Google+ Page Url{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="google_page" name="google_page" type="text" value="{$configs['google_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you have a <strong>Google+ page</strong>, please complete this input.{/t}
                              </span>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="youtube_page">
                              {t}YouTube Page Url{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="youtube_page" name="youtube_page" type="text" value="{$configs['youtube_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you have a <strong>Youtube page</strong>, please complete the form with your youtube page url.{/t}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_facebook" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_facebook" href="#facebook">
                            <i class="fa fa-facebook"></i>{t}Facebook{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="facebook" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="facebook_page">
                              {t}Facebook Page Url{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="facebook_page" name="facebook_page" type="text" value="{$configs['facebook_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you have a <strong>facebook page</strong>, please complete the form with your facebook page url and Id.{/t}
                              </span>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="facebook_id">
                              {t}Facebook Id{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="facebook_id" name="facebook_id" type="text" value="{$configs['facebook_id']|default:""}">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="facebook_api_key">
                              {t}APP key{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="facebook_api_key" name="facebook[api_key]" type="text" value="{$configs['facebook']['api_key']|default:""}">
                              <span class="help">
                                {t escape=off}You can get your Facebook App Keys from <a class="external-link" href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}
                              </span>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="facebook_secret_key">
                              {t}Secret key{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="facebook_secret_key" name="facebook[secret_key]" type="text" value="{$configs['facebook']['secret_key']|default:""}">
                            </div>
                          </div>
                          {is_module_activated name="FIA_MODULE"}
                          <div class="form-group">
                            <label class="form-label" for="facebook_instant_articles_tag">
                              {t}Instant Articles (fb:pages meta tag){/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="facebook_instant_articles_tag" name="facebook[instant_articles_tag]" type="text" value="{$configs['facebook']['instant_articles_tag']|default:""}">
                            </div>
                          </div>
                          {/is_module_activated}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_twitter" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_twitter" href="#twitter">
                            <i class="fa fa-twitter"></i>{t}Twitter{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="twitter" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="twitter_page">
                              {t}Twitter Page{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="twitter_page" name="twitter_page" type="text" value="{$configs['twitter_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you also have a <strong>Twitter page</strong>, add your page url on the form. Default will be set with Opennemas.{/t}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_instagram" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_instagram" href="#instagram">
                            <i class="fa fa-instagram"></i>{t}Instagram{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="instagram" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="instagram_page">
                              {t}Instagram Page{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="instagram_page" name="instagram_page" type="text" value="{$configs['instagram_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you also have a <strong>Instagram page</strong>, add your page url on the form.{/t}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_pinterest" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_pinterest" href="#pinterest">
                            <i class="fa fa-pinterest-square"></i>{t}Pinterest{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="pinterest" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="pinterest_page">
                              {t}Pinterest Page{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="pinterest_page" name="pinterest_page" type="text" value="{$configs['pinterest_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you also have a <strong>Pinterest page</strong>, add your page url on the form.{/t}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_vimeo" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_vimeo" href="#vimeo">
                            <i class="fa fa-vimeo-square"></i>{t}Vimeo{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="vimeo" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="vimeo_page">
                              {t}Vimeo Page{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="vimeo_page" name="vimeo_page" type="text" value="{$configs['vimeo_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you also have a <strong>Vimeo page</strong>, add your page url on the form.{/t}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="panel-group" id="accordion_linkedin" data-toggle="collapse">
                    <div class="panel panel-default">
                      <div class="panel-heading collapsed">
                        <h4 class="panel-title">
                          <a class="collapsed" data-toggle="collapse" data-parent="#accordion_linkedin" href="#linkedin">
                            <i class="fa fa-linkedin"></i>{t}LinkedIn{/t}
                          </a>
                        </h4>
                      </div>
                      <div id="linkedin" class="panel-collapse collapse" style="height: 0px;">
                        <div class="panel-body">
                          <div class="form-group">
                            <label class="form-label" for="linkedin_page">
                              {t}LinkedIn Page{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="linkedin_page" name="linkedin_page" type="text" value="{$configs['linkedin_page']|default:""}">
                              <span class="help">
                                {t escape=off}If you also have a <strong>LinkedIn page</strong>, add your page url on the form. Default will be set with Opennemas.{/t}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>

            {if $smarty.session._sf2_attributes.user->isMaster()}
            <uib-tab heading="{t}Only masters{/t}">
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-8">
                    <h4>Robots.txt</h4>
                    <div class="form-group">
                      <label class="form-label" for="robots_txt_rules">
                        {t}Robots.txt rules{/t}
                      </label>
                      <span class="help">
                        {t escape=off}Add custom robots.txt rules like 'Disallow: /tag'. Refer to the <a href="http://www.robotstxt.org/robotstxt.html" target="_blank">documentation</a>.{/t}
                      </span>
                      <div class="controls">
                        <textarea class="form-control" id="robots_txt_rules" name="robots_txt_rules" type="text" class="input-xlarge">{$configs['robots_txt_rules']|default:""}</textarea>
                      </div>
                    </div>
                    <h4>Scripts</h4>
                    <div class="form-group">
                      <label class="form-label" for="header-script">
                        {t}Scripts in header{/t}
                        <span class="help">{t}This scripts will be included before the </head> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="header-script" name="header_script">{$configs['header_script']|base64_decode|escape:'html'|default:""}</textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="body-start-script">
                        {t}Scripts at body start{/t}
                        <span class="help">{t}This scripts will be included before the <body> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="body-start-script" name="body_start_script">{$configs['body_start_script']|base64_decode|escape:'html'|default:""}</textarea>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="body-end-script">
                        {t}Scripts at body end{/t}
                        <span class="help">{t}This scripts will be included before the </body> tag{/t}</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="body-end-script" name="body_end_script">{$configs['body_end_script']|base64_decode|escape:'html'|default:""}</textarea>
                      </div>
                    </div>
                    <h4>CSS</h4>
                    <div class="form-group">
                      <label class="form-label" for="custom-css">
                        {t}Custom CSS{/t}
                        <span class="help">{t}This sripts will be included in the global.css file.{/t}</span>
                        <span class="text-danger">Not functional for now</span>
                      </label>
                      <div class="controls">
                        <textarea class="form-control" id="custom-css" name="custom_css" disabled="disabled" readonly="readonly">{$configs['custom_css']|stripslashes|default:""}</textarea>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <h4>RSS</h4>
                    <div class="form-group">
                      <label class="form-label" for="items_per_page">
                        {t}Items in RSS{/t}
                      </label>
                      <div class="controls">
                        <input class="form-control" id="elements_in_rss" name="elements_in_rss" type="number" value="{$configs['elements_in_rss']|default:10}">
                      </div>
                    </div>
                    <h4>{t}Redirection{/t}</h4>
                    <div class="form-group">
                      <div class="checkbox">
                        <input {if $configs['redirection'] eq "1"}checked{/if} id="redirection" name="redirection" type="checkbox" value="1">
                        <label for="redirection">
                          {t}Redirect to frontpage non-migrated contents{/t}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>
            {/if}
          </uib-tabset>
        </div>
      </div>
    </div>
  </form>
{/block}
