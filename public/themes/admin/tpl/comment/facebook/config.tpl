{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_comments_facebook_config}" method="POST">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-comment"></i>
                {t}Comments{/t}
              </h4>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h5>{t}Facebook{/t}</h5>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <h5>{t}Settings{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=admin_comments_facebook}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <a class="btn btn-link change" data-controls-modal="modal-comment-change" href="#" title="{t}Change comments module{/t}">
                  <i class="fa fa-refresh"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" type="submit">
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
      <div class="grid simple">
        <div class="grid-body">
          <p>{t escape=off}To be able to moderate comments of your site in Facebook you must create and set here your <strong>Facebook App Id</strong>.{/t}</p>
          <div class="form-group">
            <label class="form-label" for="facebook_api_key">
              Facebook App Id
            </label>
            <div class="controls">
              <input class="form-control" id="facebook_api_key" name="facebook[api_key]" type="text" value="{$fb_app_id|default:""}"/>
              <div class="help-block">
                {t escape=off}You can get your Facebook App Keys from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers website</a>.{/t}
              </div>
            </div>
          </div>
        </div>
      </div>
      {include file="comment/partials/_config.tpl"}
    </div>
  </form>
{/block}

{block name="modals"}
  {include file="comment/modals/_modalChange.tpl"}
{/block}
