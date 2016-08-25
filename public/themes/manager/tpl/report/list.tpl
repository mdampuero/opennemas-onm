<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_reports_list') %]">
              <i class="fa fa-files-o"></i>
              {t}Reports{/t}
            </a>
          </h4>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="page-navbar filters-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <div class="input-group input-group-animated">
            <span class="input-group-addon">
              <span class="fa fa-search fa-lg"></span>
            </span>
            <input class="input-min-45 input-200" ng-model="criteria.title" placeholder="{t}Search by name{/t}" type="text" style="width:250px;"/>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="items">
  <div class="p-b-100 p-t-100 text-center" ng-if="items.length == 0">
    <i class="fa fa-7x fa-user-secret"></i>
    <h2 class="m-b-50">{t}There is nothing to see here, kid.{/t}</h2>
  </div>
  <div class="grid simple" ng-if="items.length > 0">
    <div class="grid-body no-padding">
      <div class="grid-overlay" ng-if="loading"></div>
      <div class="table-wrapper">
        <table class="table no-margin">
          <thead>
            <tr>
              <th>{t}Report{/t}</th>
              <th class="text-center" width="150">{t}Action{/t}</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="item in items | filter: { title: criteria.title  }">
              <td>
                [% item.title %]
                <div class="help-block">
                  [% item.description %]
                </div>
              </td>
              <td class="text-center">
                <a class="btn btn-success text-uppercase" ng-href="{url name=manager_ws_reports_csv}?id=[% item.id %]&token=[% security.token %]">
                  <i class="fa fa-download m-r-5"></i>
                  {t}Download{/t}
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
