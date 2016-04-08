<div class="page-navbar selected-navbar collapsed" ng-class="{ 'collapsed': selected.items.length == 0 }">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section pull-left">
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="deselectAll()" uib-tooltip="{t}Clear selection{/t}" tooltip-placement="bottom"type="button">
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
            <a class="btn btn-link" ng-href="{url name=manager_ws_instances_csv}?ids=[% selected.instances.join(); %]&token=[% token %]" uib-tooltip="{t}Download CSV of selected{/t}" tooltip-placement="bottom">
              <i class="fa fa-download fa-lg"></i>
            </a>
          </li>
        {/if}
        {if $list === 'notification'}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('fixed', 0)" uib-tooltip="{t}Unfixed{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-unlock fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('fixed', 1)" uib-tooltip="{t}Fixed{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-lock fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('forced', 0)" uib-tooltip="{t}Unforced{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-eye-slash fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('forced', 1)" uib-tooltip="{t}Forced{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-eye fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('enabled', 0)" uib-tooltip="{t}Disabled{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="patchSelected('enabled', 1)" uib-tooltip="{t}Enabled{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
        {/if}
        {if $list !== 'notification' && list !== 'user_group'}
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="setEnabledSelected(0)" uib-tooltip="{t}Disable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-times fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <button class="btn btn-link" ng-click="setEnabledSelected(1)" uib-tooltip="{t}Enable{/t}" tooltip-placement="bottom" type="button">
              <i class="fa fa-check fa-lg"></i>
            </button>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
        {/if}
        <li class="quicklinks">
          <button class="btn btn-link" ng-click="deleteSelected()" uib-tooltip="{t}Delete{/t}" tooltip-placement="bottom" type="button">
            <i class="fa fa-trash-o fa-lg"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</div>