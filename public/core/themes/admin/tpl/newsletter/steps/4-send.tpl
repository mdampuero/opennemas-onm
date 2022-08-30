{extends file="base/admin.tpl"}

{block name="content"}
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-envelope m-r-10"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                {t}Newsletters{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <h4>{t}Send{/t}</h4>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content newsletter-manager" ng-init="step = 3">
    {include file="newsletter/partials/send_steps.tpl"}
    <div class="text-center">
      <i class="fa fa-envelope fa-4x text-success"></i>
      <h3>{t}A Newsletter send action is being processed in background{/t}</h3>
      <h5>
        {t}You can go back to the list{/t}
      </h5>
    </div>
    <div class="row m-t-30">
      <div class="newsletter-report">
        <div class="newsletter-report-list">
        </div>
        <div class=" m-t-20 m-r-15 m-b-15 m-t-15 m-l-15">
          <a class="btn-block btn btn-lg btn-success" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
            <i class="fa fa-reply"></i>
            {t}Go back to the list{/t}
          </a>
        </div>
      </div>
    </div>
  </div>
{/block}
