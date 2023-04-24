<div class="checkbox p-t-5">
  <button class="btn btn-white" style="border:0;" ng-click="postponed = !postponed" uib-tooltip="{t}Scheduled{/t}" tooltip-placement="bottom">
    <i class="fa fa-square-o" aria-hidden="true" ng-if="!postponed"></i><i class="fa fa-clock-o m-l-5" aria-hidden="true" ng-if="!postponed"></i>
    <i class="fa fa-check-square-o text-primary" aria-hidden="true" ng-if="postponed"></i><i class="fa fa-clock-o m-l-5" aria-hidden="true" ng-if="postponed"></i>
  </button>
</div>
