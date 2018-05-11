{extends file="base/admin.tpl"}

{block name="content"}
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                <i class="fa fa-envelope"></i>
                {t}Newsletters{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <div class="p-l-10 p-r-10 p-t-10">
              <i class="fa fa-angle-right"></i>
            </div>
          </li>
          <li class="quicklinks hidden-xs">
            <h5><strong>{t}Send{/t}</strong></h5>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content newsletter-manager" ng-init="step = 3">
    {include file="newsletter/partials/send_steps.tpl"}
    <div class="text-center">
      <i class="fa fa-envelope fa-4x text-success"></i>
      <h3>{t}Newsletter sending report{/t}</h3>
      <h5>{t escape=off 1=$send_report['total']}We queued <strong>%1 emails</strong> to be sent. Please, find below the report.{/t}</h5>
    </div>
    <div class="row m-t-30">
      <div class="newsletter-report">
        <div class="newsletter-report-list">
        {foreach $send_report['report'] as $item}
          <div class="p-r-15 p-b-15 p-t-15 p-l-15" style="border-bottom: 1px solid #ccc">
            {if $item[0]->type == 'external'}
              <i class="fa fa-external-link m-r-10" uib-tooltip="{t}External service{/t}"></i>
            {/if}
            {if $item[0]->type == 'list'}
              <i class="fa fa-address-book m-r-10" uib-tooltip="{t}Subscription list{/t}"></i>
            {/if}
            {if $item[0]->type == 'email'}
              <i class="fa fa-envelope m-r-10" uib-tooltip="{t}Email address{/t}"></i>
            {/if}

            {$item[0]->name}
            {if $item[1]}
            <i class="fa fa-check text-success"></i>
            <span class="text-success">{$item[2]}</span>
            {else}
            <i class="fa fa-times text-danger"></i>
            <span class="text-danger">{$item[2]}</span>
            {/if}
          </div>
        {/foreach}
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
