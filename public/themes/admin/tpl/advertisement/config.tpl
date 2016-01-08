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
  <div class="content">
    <div class="grid simple">
      <div class="grid-body">
        <div class="form-group">
          <label for="ads_settings_lifetime_cookie" class="form-label">
            {t}Cookie lifetime for intersticials{/t}
            <span class="help">{t}This setting indicates how long will take to re-display the interstitial in frontpage.{/t} {t}(in minutes){/t}</span>
          </label>
          <div class="controls">
            <input type="number" class="form-control" name="ads_settings_lifetime_cookie" id="ads_settings_lifetime_cookie" value="{$configs['ads_settings']['lifetime_cookie']|default:'300'}" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="ads_settings_no_generics" class="form-label">{t}Allow generic advertisement{/t}</label>
          <div class="controls">
            <select name="ads_settings_no_generics" id="ads_settings_no_generics">
              <option value="0">{t}Yes{/t}</option>
              <option value="1" {if $configs['ads_settings']['no_generics'] eq "1"} selected {/if}>{t}No{/t}</option>
            </select>
            <div class="help-block">{t}This settings allow printing home ads when ads in category are empty.{/t}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="grid simple">
      <div class="grid-title">
        <h5>{t}OpenX/Revive Ad server integration{/t}</h5>
      </div>
      <div class="grid-body">
        <div class="form-group">
          <label for="revive_ad_server_url" class="form-label">{t}Ad server base url{/t}</label>
          <div class="controls">
            <input class="form-control" type="text" name="revive_ad_server_url" value="{$configs['revive_ad_server']['url']}">
            <div class="help-block">{t}The ad server URL (i.e. http://ad.serverexample.net/).{/t}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="grid simple">
      <div class="grid-title">
        <h5>{t}Tradedoubler integration{/t}</h5>
      </div>
      <div class="grid-body">
        <div class="form-group">
          <label for="tradedoubler_id" class="form-label">{t}Tradedoubler ID{/t}</label>
          <div class="controls">
            <input class="form-control" type="number" name="tradedoubler_id" value="{$configs['tradedoubler_id']}">
            <div class="help-block">{t}Only the ID from Tradedoubler validation tag{/t}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
