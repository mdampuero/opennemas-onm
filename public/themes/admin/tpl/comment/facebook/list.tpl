{extends file="base/admin.tpl"}

{block name="content"}
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
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=admin_comments_facebook_config}" title="{t}Config facebook module{/t}">
                <i class="fa fa-gear fa-lg"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body">
        <h3>
          {t}You are now using Facebook comments{/t}
        </h3>
        {if empty($fb_app_id)}
        <p>
        {t escape=off}If you want to moderate comments, you first need to create a Facebook application in <a href="https://developers.facebook.com/" target="_blank">here</a> to get an application Id and then click on settings to configure it{/t}.
        </p>
        {else}
        <p>
        {t escape=off}To moderate your comments go to <a href="https://developers.facebook.com/tools/comments" target="_blank">Facebook moderation tool page</a>{/t}.
        </p>
        {/if}
      </div>
    </div>
  </div>
{/block}

{block name="copyright"}
{/block}
