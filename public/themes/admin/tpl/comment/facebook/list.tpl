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
              <a class="btn btn-link" href="{url name=backend_comments_facebook_config}" title="{t}Config facebook module{/t}">
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
      <div class="grid-body text-center">
        {if !empty($fb_app_id)}
          <i class="fa fa-4x fa-comment text-warning m-b-10 m-t-30"></i>
          <h4 class="modal-title m-b-10">{t 1="Facebook"}Your are using the external comment system "%1"{/t}</h4>
          <p>{t escape=off}To moderate your comments, go to <a href="https://developers.facebook.com/tools/comments" target="_blank">Facebook moderation tool page</a>{/t}</p>
        {else}
          <i class="fa fa-4x fa-warning text-warning m-b-10 m-t-40"></i>
          <h4>{t}Facebook comments are not configured{/t}</h4>
          <p>
            {t escape=off}If you want to moderate comments, you first need to create a Facebook application in <br><a href="https://developers.facebook.com/" target="_blank">here</a> to get an application Id and then click on settings to configure it{/t}.
          </p>
        {/if}
      </div>
    </div>
  </div>
{/block}

{block name="copyright"}
{/block}
