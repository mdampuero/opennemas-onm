{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_comments_disqus_config}" method="POST">
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
              <h5>{t}Disqus{/t}</h5>
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
                <a class="btn btn-link" href="{url name=admin_comments_disqus}" title="{t}Go back to list{/t}">
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
        <div class="grid-title">
          <h4>{t}Set your Disqus configuration{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="shortname">
              Disqus Id (shortname)
            </label>
            <div class="controls">
              <input class="form-control" id="shortname" name="shortname" required type="text" value="{$shortname|default:""}"/>
              <div class="help-block">
                {t}A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site are referenced with the shortname{/t}.
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="secret_key">
              Disqus API Secret Key
            </label>
            <div class="controls">
              <input class="form-control" id="secret_key" name="secret_key"required type="text" value="{$secretKey|default:""}"/>
              <div class="help-block">
                {t escape=off}You can get your Disqus secret key in <a href="http://disqus.com/api/applications/" target="_blank">here</a>{/t}.
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
