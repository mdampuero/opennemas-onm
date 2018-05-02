{extends file="base/admin.tpl"}

{block name="content"}
  <form action="{url name=admin_comments_disqus_config}" method="POST">
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
          <div class="form-group m-l-20">
            <label class="form-label" for="shortname">
              Disqus Id (shortname)
            </label>
            <div class="controls">
              <input class="form-control" id="shortname" name="shortname" required type="text" value="{$shortname|default:""}"/>
              <div class="help">
                {t}A shortname is the unique identifier assigned to a Disqus site. All the comments posted to a site are referenced with the shortname{/t}.
              </div>
            </div>
          </div>
          <div class="form-group m-l-20">
            <label class="form-label" for="secret_key">
              Disqus API Secret Key
            </label>
            <div class="controls">
              <input class="form-control" id="secret_key" name="secret_key"required type="text" value="{$secretKey|default:""}"/>
              <div class="help">
                {t escape=off}You can get your Disqus secret key in <a href="http://disqus.com/api/applications/" target="_blank">here</a>{/t}.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}

{block name="modals"}
  {include file="comment/modals/_modalChange.tpl"}
{/block}
