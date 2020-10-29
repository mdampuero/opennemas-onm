<span class="form-status {$iClass}">
  {if $iCounter}
    <span class="form-status-item">
      <span class="ng-cloak badge badge-default" ng-class="{ 'badge-warning': item.{$iField}.length >= 50 &amp;&amp; item.{$iField}.length < 80, 'badge-danger': item.{$iField}.length >= 80 }">
        <strong>
          [% item.{$iField} ? item.{$iField}.length : 0 %]
        </strong>
      </span>
    </span>
  {/if}
  {if $iValidation}
    <span class="form-status-item" ng-class="{ 'has-error': item.{$iField} && form.{$iForm}.$dirty && form.{$iForm}.$invalid, 'has-info': !item.{$iField} || !form.{$iForm}.$dirty && form.{$iForm}.$invalid }">
      {if $iFlag}
        <span class="fa fa-circle-o-notch fa-spin" ng-if="flags.http.{$iFlag}"></span>
      {/if}
      <span class="fa fa-check text-success" ng-if="{if $iFlag}!flags.http.{$iFlag} && {/if}item.{$iField} && form.{$iForm}.$valid"></span>
      <span class="fa fa-info-circle text-info" ng-if="{if $iFlag}!flags.http.{$iFlag} && {/if}!item.{$iField} && form.{$iForm}.$invalid" tooltip-class="tooltip-right" uib-tooltip="{t}This field is required{/t}"></span>
      <span class="fa fa-times text-error" ng-if="{if $iFlag}!flags.http.{$iFlag} && {/if}item.{$iField} && form.{$iForm}.$dirty && form.{$iForm}.$invalid" tooltip-class="tooltip-right" uib-tooltip="{t}This field is invalid{/t}"></span>
    </span>
  {/if}
</span>
