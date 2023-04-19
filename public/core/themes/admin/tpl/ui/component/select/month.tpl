<select name="month" ng-model="{$ngModel}">
  <option value="">{t}All months{/t}</option>
  <optgroup label="[% year.name %]" ng-repeat="year in {$data}">
    <option value="[% month.value %]" ng-repeat="month in year.months">
    [% month.name %] ([% year.name %])
    </option>
  </optgroup>
</select>
