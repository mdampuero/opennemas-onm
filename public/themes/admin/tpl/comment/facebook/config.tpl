{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_comments_facebook_config}" method="POST">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="{url name=admin_comments}" title="{t}Go back to list{/t}">
                  <i class="fa fa-comment"></i>
                  {t}Comments{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <div class="p-l-10 p-r-10 p-t-10">
                <i class="fa fa-angle-right"></i>
              </div>
            </li>
            <li class="quicklinks hidden-xs">
              <h5><strong>{t}Settings{/t}</strong></h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-success" type="submit">
                  <i class="fa fa-save"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      {include file="comment/partials/_config.tpl"}

      <div class="grid simple">
        <div class="grid-body">
          <h4>
            <i class="fa fa-cog"></i>
            {t}Configuration{/t}
          </h4>
          <div class="form-group m-l-25">
            <label class="form-label" for="facebook_api_key">
              Facebook App Id
            </label>
            <div class="controls">
              <input class="form-control" id="facebook_api_key" name="facebook[api_key]" type="text" value="{$fb_app_id|default:""}"/>
              <div class="help">
                {t escape=off}To be able to moderate comments of your site in Facebook you must create and set here your <strong>Facebook App Id</strong>.{/t}
                <br>
                {t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
