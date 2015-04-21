<div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section pull-left">
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="deselectAll()" tooltip="{t}Clear selection{/t}" tooltip-placement="bottom"type="button">
            <i class="fa fa-arrow-left fa-lg"></i>
          </button>
        </li>
        <li class="quicklinks">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h4>
            [% selected.items.length %] <span class="hidden-xs">{t}items selected{/t}</span>
          </h4>
        </li>
      </ul>
      <ul class="nav quick-section pull-right">
        {if $list === 'instance'}
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="{url name=manager_ws_instances_csv}?ids=[% selected.instances.join(); %]&token=[% token %]" tooltip="{t}Download CSV of selected{/t}" tooltip-placement="bottom">
              <i class="fa fa-download fa-lg"></i>
            </a>
          </li>
        {/if}
        {if $list !== 'user_group'}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="setEnabledSelected(0)" tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="setEnabledSelected(1)" tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
        {/if}
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="deleteSelected()" tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</div>
