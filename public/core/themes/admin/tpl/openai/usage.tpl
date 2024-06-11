{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="OpenAIUsageCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_openai_config') %]">
                  <i class="fa fa-line-chart m-r-10"></i>
                  {t}OpenAI Module Usage{/t}
                </a>
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple onm-shadow">
        <div class="grid-title">
          <h4>{t}Tokens used per model{/t}</h4>
        </div>
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-xs-12 col-md-3" ng-repeat="(model, value) in tokens">
              <div class="col-xs-12">
                <div class="row center-text">
                  <h4>[% model %]</h4>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-md-6">
                  <h5>{t}Prompt tokens{/t}
                    <i class="fa fa-info-circle text-info m-t-2" ng-if="$first" uib-tooltip-html="'{t}Related to the input fields length (Input){/t}'" tooltip-placement="bottom"></i>
                  </h5>
                </div>
               <div class="col-xs-12 col-md-6 center-text">
                  <h4>[% value.prompt_tokens || 0 %]</h4>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-md-6">
                  <h5>{t}Completion tokens{/t}
                    <i class="fa fa-info-circle text-info m-t-2" ng-if="$first" uib-tooltip-html="'{t}Related to the text returned by the AI (Output){/t}'" tooltip-placement="bottom"></i>
                  </h5>
                </div>
               <div class="col-xs-12 col-md-6 center-text">
                  <h4>[% value.completion_tokens || 0 %]</h4>
                </div>
              </div>
              <hr/>
              <div class="row">
                <div class="col-xs-12 col-md-6">
                  <h5>{t}Total tokens{/t}</h5>
                </div>
               <div class="col-xs-12 col-md-6 center-text">
                  <h4><strong>[% value.total_tokens || 0 %]</strong></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple onm-shadow">
        <div class="grid-title">
          <h4>{t}Pricing{/t}</h4>
          <h5>
            <i class="fa fa-info-circle text-info m-t-2" ></i>
            {t}All prices are in US${/t}
          </h5>
        </div>
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-xs-12 col-md-3 m-r-50" ng-repeat="(model, price) in prices">
              <div class="col-xs-12">
                <div class="row center-text">
                  <h4>[% model %]</h4>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-md-6">
                  <h4>{t}Input{/t}</h4>
                </div>
               <div class="col-xs-12 col-md-6 center-text">
                  <h4>[% price.input %] US$ / 1M Tokens</h4>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 col-md-6">
                  <h4>{t}Output{/t}</h4>
                </div>
               <div class="col-xs-12 col-md-6 center-text">
                  <h4>[% price.output %] US$ / 1M Tokens</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple onm-shadow">
        <div class="grid-title">
          <h4>{t}Total Spent{/t}</h4>
        </div>
        <div class="grid-body ng-cloak">
          <div class="row">
            <h5>{t}Total{/t}: ~ <b>[% total %]</b> US$
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
