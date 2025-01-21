{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="OpenAIDashboardCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_openai_config') %]">
                  <i class="fa fa-tachometer m-r-10"></i>
                  {t}Dashboard{/t}
                </a>
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      {if !in_array("es.openhost.module.openai", $app.instance->activated_modules)}
        <div class="grid simple m-b-2">
          <div class="grid-body bg-transparent">
            <div class="bg-white onm-shadow p-15">
              <h2>{t}Improve your manager{/t}</h2>
                <p class="lead">{t}Contact with us to enjoy this feature.{/t}</p>
                <a class="btn btn-success btn-lg btn-lg-onm btn-block" href="mailto:sales@openhost.es" role="button" target="_blank">{t}I want this module{/t}</a>
            </div>
          </div>
        </div>
      {/if}
      <div class="grid simple p-t-0">
        <div class="grid-body bg-transparent ng-cloak ">
          <div class="row">
            <div class="col-sm-4">
              <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15 bg-white cursor-pointer" ng-class="{ 'bg-white': panelSelected == 'words' }" ng-click="generateStats('words')">
                <label class="form-label">{t}Total words managed{/t}</label>
                <div class="form-status text-left">
                  <p class="onm-score text-left lead m-b-0">
                    <strong ng-if="totals.words">[% totals.words.total | number : 0 %]</strong>
                    <strong ng-if="!totals.words">0</strong>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15 cursor-pointer" ng-class="{ 'bg-white': panelSelected == 'usage' }" ng-click="generateStats('usage')">
                <label class="form-label">{t}Total interactions{/t}</label>
                <div class="form-status text-left">
                  <p class="onm-score text-left lead m-b-0">
                    <strong ng-if="totals.usage">[% totals.usage.total | number : 0 %]</strong>
                    <strong ng-if="!totals.usage">0</strong>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15 cursor-pointer" ng-class="{ 'bg-white': panelSelected == 'price' }" ng-click="generateStats('price')">
                <div class="clearfix">
                  <label class="form-label pull-left">
                    {t}Total{/t}
                  </label>
                  <label class="form-label pull-right">
                    <span class="" uib-tooltip="{t}Average price per 1 million words sent{/t}" tooltip-placement="left">
                      <i class="fa fa-arrow-up text-info"></i> &asymp; {t} [% (totals.price.input / totals.words.input * 1000000) | number : 4 %] €/M{/t}
                    </span>
                    &nbsp;
                    <span class="" uib-tooltip="{t}Average price per 1 million words received{/t}" tooltip-placement="left">
                      <i class="fa fa-arrow-down text-success"></i> &asymp; {t}[% (totals.price.output / totals.words.output * 1000000) | number : 4 %] €/M{/t}
                    </span>
                  </label>
                </div>
                <div class="form-status text-left">
                  <p class="onm-score text-left lead m-b-0">
                    <strong ng-if="totals.price">[% totals.price.total | number : 2 %] €</strong>
                    <strong ng-if="!totals.price">0</strong>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="row m-t-15">
            <div class="col-xs-12">
              <div class="panel bg-white onm-shadow">
                <div class="panel-heading m-t-10">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="lead" ng-if="panelSelected == 'words'">{t}Total words managed{/t}</div>
                      <div class="lead" ng-if="panelSelected == 'usage'">{t}Total interactions{/t}</div>
                      <div class="lead" ng-if="panelSelected == 'price'">{t}Total{/t}</div>
                    </div>
                    <div class="col-sm-6 text-right">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-default" ng-click="moveToPreviousMonths()"><i class="fa fa-chevron-left"></i></button>
                        <button type="button" class="btn btn-sm btn-default bg-white" style="width: 150px;">[% labelFilter %]</button>
                        <button type="button" class="btn btn-sm btn-default" ng-click="moveToNextMonths()"><i class="fa fa-chevron-right"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel-body">
                  <div>
                    <canvas id="myChart" class="chart chart-bar" chart-data="data" chart-labels="labels" chart-series="series" chart-options="options" style="height: 400px;"></canvas>
                  </div>
                  <div class="row m-b-20">
                    <div class="col-sm-6 col-md-3">
                      <div class="m-t-30 showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15">
                        <table class="table table-striped ">
                          <thead>
                            <tr>
                              <th class="pointer">
                                {t}Day{/t}
                              </th>
                              <th width="150" class="text-right">
                                {t}Total{/t}
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr ng-repeat="item in labels | limitTo: 8 : 0">
                              <td>[% item %]</td>
                              <td class="text-right"><b>[% data[$index] %] <span ng-if="panelSelected == 'price'">€</span></b></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="m-t-30 showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15">
                        <table class="table table-striped ">
                          <thead>
                            <tr>
                              <th class="pointer">
                                {t}Day{/t}
                              </th>
                              <th width="150" class="text-right">
                                {t}Total{/t}
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr ng-repeat="item in labels | limitTo: 8 : 8">
                              <td>[% item %]</td>
                              <td class="text-right"><b>[% data[$index + 8] %] <span ng-if="panelSelected == 'price'">€</span></b></b></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="m-t-30 showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15">
                        <table class="table table-striped ">
                          <thead>
                            <tr>
                              <th class="pointer">
                                {t}Day{/t}
                              </th>
                              <th width="150" class="text-right">
                                {t}Total{/t}
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr ng-repeat="item in labels | limitTo: 8 : 16">
                              <td>[% item %]</td>
                              <td class="text-right"><b>[% data[$index + 16] %] <span ng-if="panelSelected == 'price'">€</span></b></b></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                      <div class="m-t-30 showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15">
                        <table class="table table-striped ">
                          <thead>
                            <tr>
                              <th class="pointer">
                                {t}Day{/t}
                              </th>
                              <th width="150" class="text-right">
                                {t}Total{/t}
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr ng-repeat="item in labels | limitTo: (labels.length - 24) : 24">
                              <td>[% item %]</td>
                              <td class="text-right"><b>[% data[$index + 24] %] <span ng-if="panelSelected == 'price'">€</span></b></b></td>
                            </tr>
                            <tr ng-if="labels.length > 0" ng-repeat="i in [].constructor(32 - labels.length) track by $index">
                                <td colspan="2" class="text-center bg-white">&nbsp;</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
