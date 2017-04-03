{extends file="base/admin.tpl"}

{block name="header-css" append}
  <style type="text/css">
    .disqus .disqus-link { text-align: center;width: 25%;margin: 100px auto;padding: 15px 0;background-color: #444;}
    .disqus a .disqus-link { font-size: 1.4em;color: #fff;}
    .disqus a:hover { text-decoration: none;}
    .disqus a:hover .disqus-link { background-color: #666;}
    .disqus a .disqus-link img { width: 30px;margin-right: 10px;}
  </style>
{/block}

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
              <a class="btn btn-link" href="{url name=admin_comments_disqus_config}" title="{t}Disqus module configuration{/t}">
                <i class="fa fa-gear fa-lg"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="disqus">
      {if !empty($disqus_shortname) && !empty($disqus_secret_key)}
        <a href="http://{$disqus_shortname}.disqus.com/admin/moderate/" target="_blank">
          <div class="disqus-link">
            <img src="{$_template->getImageDir()}/disqus-icon.png" alt="Disqus" />
            {t}To moderate your Disqus comments, click here{/t}
          </div>
        </a>
      {else}
        <div class="wrapper-content center">
          <h3>{t}Disqus not configured{/t}</h3>
        </div>
      {/if}
    </div>
  </div>
{/block}
