{extends file="base/admin.tpl"}

{block name="content"}
  <div>
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-shopping-cart page-navbar-icon"></i>
                {t}Purchases{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=backend_purchases_list}" title="{t}Go back{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content no-animate">
      <div class="text-center">
        <h1><i class="fa fa-file-pdf-o"></i></h1>
        <h3>{t}Your invoice is currently unavailable.{/t}</h3>
        <h4>{t escape=off}Please, contact our <a href="javascript:UserVoice.showPopupWidget();">support team</a>.{/t}</h4>
      </div>
    </div>
  </div>
{/block}
