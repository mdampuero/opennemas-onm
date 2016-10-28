{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_ads_config}" method="POST">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home fa-lg"></i>
              <span class="hidden-xs">{t}Advertisements{/t}</span>
              <span class="visible-xs-inline">{t}Ads{/t}</span>
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Configuration{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li>
              <a href="{url name=admin_ads}" class="btn btn-link" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <span class="fa fa-reply"></span>
              </a>
            </li>
            <li class="quicklinks"><span class="h-seperate"></span></li>
            <li>
              <button type="submit" class="btn btn-primary">
                <span class="fa fa-save"></span> {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content ng-cloak">
    <uib-tabset>
      <uib-tab heading="{t}General{/t}">
        <div class="form-group">
          <label for="ads_settings_lifetime_cookie" class="form-label">
            {t}Cookie lifetime for intersticials{/t}
          </label>
          <div class="controls">
            <input type="number" class="form-control" name="ads_settings_lifetime_cookie" id="ads_settings_lifetime_cookie" value="{$configs['ads_settings']['lifetime_cookie']|default:'300'}" required/>
            <span class="help">{t}This setting indicates how long will take to re-display the interstitial in frontpage.{/t} {t}(in minutes){/t}</span>
          </div>
        </div>
        <div class="form-group">
          <label for="ads_settings_no_generics" class="form-label">{t}Allow generic advertisement{/t}</label>
          <div class="controls">
            <select name="ads_settings_no_generics" id="ads_settings_no_generics">
              <option value="0">{t}Yes{/t}</option>
              <option value="1" {if $configs['ads_settings']['no_generics'] eq "1"} selected {/if}>{t}No{/t}</option>
            </select>
            <div class="help">{t}This settings allow printing home ads when ads in category are empty.{/t}</div>
          </div>
        </div>
      </uib-tab>
      <uib-tab heading="{t}External services{/t}">
        <h4>{t}OpenX/Revive Ad server integration{/t}</h4>
        <div class="form-group">
          <label for="revive_ad_server_url" class="form-label">{t}Ad server base url{/t}</label>
          <div class="controls">
            <input class="form-control" type="text" name="revive_ad_server_url" value="{$configs['revive_ad_server']['url']}">
            <div class="help">{t}The ad server URL (i.e. http://ad.serverexample.net/).{/t}</div>
          </div>
        </div>
        <h4>{t}DFP category targeting{/t}</h4>
        <div class="form-group">
          <label for="dfp_options_target" class="form-label">{t}Key for setTargeting function{/t}</label>
          <div class="controls">
            <input class="form-control" type="text" name="dfp_options_target" value="{$configs['dfp_options']['target']}">
            <div class="help">{t}Set a key for targeting your ads by category. Note that the value for targeting will always be the current category internal name{/t}</div>
          </div>
        </div>
        {if $smarty.session._sf2_attributes.user->isMaster()}
        <h4>{t}DFP custom code{/t}</h4>
        <div class="form-group">
          <label for="dfp_custom_code" class="form-label">{t}Custom code at the end of DFP tags{/t}</label>
          <div class="controls">
            <textarea class="form-control" name="dfp_custom_code">{$configs['dfp_custom_code']|base64_decode}</textarea>
          </div>
        </div>
        {/if}
        <h4 class="m-t-30">{t}Tradedoubler integration{/t}</h4>
        <div class="form-group">
          <label for="tradedoubler_id" class="form-label">{t}Tradedoubler ID{/t}</label>
          <div class="controls">
            <input class="form-control" type="number" name="tradedoubler_id" value="{$configs['tradedoubler_id']}">
            <div class="help">{t}Only the ID from Tradedoubler validation tag{/t}</div>
          </div>
        </div>
        {is_module_activated name="IADBOX_MANAGER"}
        <h4 class="m-t-30">{t}Iadbox integration{/t}</h4>
        <div class="form-group">
          <label for="iadbox_id" class="form-label">{t}Iadbox ID{/t}</label>
          <div class="controls">
            <input class="form-control" type="text" name="iadbox_id" value="{$configs['iadbox_id']}">
            <div class="help">{t}Iadbox affiliate ID{/t}</div>
          </div>
        </div>
        {/is_module_activated}
      </uib-tab>
    </uib-tabset>
  </div>
</form>
{/block}
