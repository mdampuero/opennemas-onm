<div class="input-group">
  <span class="input-group-btn">
    <button class="btn btn-default dropdown-toggle b-r-0" data-toggle="dropdown" ng-style="{ 'background-color': {$ngModel} }" type="button">
      &nbsp;&nbsp;&nbsp;&nbsp;
    </button>
    <ul class="dropdown-menu dropdown-menu-right no-padding pull-right" style="min-width: 150px; overflow: hidden;">
      <li ng-click="{$ngModel}='#000000'" style="background-color: #000000; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#e1e1e1'" style="background-color: #e1e1e1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#ece5f1'" style="background-color: #ece5f1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#e8edfa'" style="background-color: #e8edfa; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#ffffff'" style="background-color: #ffffff; box-shadow: inset 0 0 0 1px #d1dade; cursor: pointer; float: left; height: 30px; width: 30px;">
        <span style="background: #980101; display: block; height: 1px; width: 40px; margin-left: -5px; margin-top: 15px; transform: rotate(-45deg)"></span>
      </li>
      <li ng-click="{$ngModel}='#980101'" style="background-color: #980101; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#dc2127'" style="background-color: #dc2127; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#ff887c'" style="background-color: #ff887c; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#ffb878'" style="background-color: #ffb878; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#ffe5d1'" style="background-color: #ffe5d1; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#fbd75b'" style="background-color: #fbd75b; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#fcfbdf'" style="background-color: #fcfbdf; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#e3f7e2'" style="background-color: #e3f7e2; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#7ae7bf'" style="background-color: #7ae7bf; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#46d6db'" style="background-color: #46d6db; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#7bd148'" style="background-color: #7bd148; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#51b749'" style="background-color: #51b749; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#5484ed'" style="background-color: #5484ed; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#a4bdfc'" style="background-color: #a4bdfc; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
      <li ng-click="{$ngModel}='#dbadff'" style="background-color: #dbadff; cursor: pointer; float: left; height: 30px; width: 30px;"></li>
    </ul>
  </span>
  <input class="form-control" colorpicker="hex" ng-model="{$ngModel}" type="text">
  <span class="input-group-btn">
    <button class="btn btn-default" ng-click="{$ngModel} = '#ffffff'" type="button">
      {t}Reset{/t}
    </button>
  </span>
</div>
