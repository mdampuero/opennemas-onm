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
            <h5>{t}Disqus{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name=backend_comments_disqus_config}" title="{t}Disqus module configuration{/t}">
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
        {if !empty($disqus_shortname) && !empty($disqus_secret_key)}
          <i class="fa fa-4x fa-comment text-warning m-b-10 m-t-40"></i>
          <h4 class="modal-title m-b-10">{t 1="Disqus"}Your are using the external comment system "%1"{/t}</h4>
          <p>{t escape=off 1=$disqus_shortname}To moderate your comments, <a href="http://%1.disqus.com/admin/moderate/" target="_blank">click here</a>{/t}</p>
        {else}
          <i class="fa fa-4x fa-warning text-warning m-b-10 m-t-40"></i>
          <h3>{t}Disqus not configured{/t}</h3>
        {/if}
      </div>
    </div>
  </div>
{/block}
