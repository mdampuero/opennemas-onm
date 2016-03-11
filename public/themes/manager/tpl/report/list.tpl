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
        <li class="m-r-10 input-prepend inside search-input no-boarder">
          <span class="add-on">
            <span class="fa fa-search fa-lg"></span>
          </span>
          <input class="no-boarder" ng-model="criteria.title" placeholder="{t}Search by name{/t}" type="text" style="width:250px;"/>
        </li>
      </ul>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="items">
  <div class="grid simple">
    <div class="grid-body no-padding">
      <div class="grid-overlay" ng-if="loading"></div>
      <div class="table-wrapper">
        <table class="table table-hover no-margin">
          <thead>
            <tr>
              <th>{t}Report{/t}</th>
              <th class="text-center" width="150">{t}Action{/t}</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-if="items.length == 0">
              <td class="empty" colspan="10">{t}There is no available instances yet{/t}</td>
            </tr>
            <tr ng-if="items.length >= 0" ng-repeat="item in items|filter:criteria" ng-class="{ row_selected: isSelected(item.id) }">
              <td>
                [% item.title %]
                <div class="help-block">
                  [% item.description %]
                </div>
              </td>
              <td class="text-center">
                <a class="btn btn-primary" ng-href="{url name=manager_ws_reports_csv}?id=[% item.id %]&token=[% token %]">
                  <i class="fa fa-download"></i>
                  {t}Download{/t}
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="text-center" ng-if="items.length == 0">
        {t escape=off}There is no reports created yet or <br/>your search don't match your criteria{/t}
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
