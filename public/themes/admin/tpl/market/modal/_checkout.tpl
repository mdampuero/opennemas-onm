  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h4 class="modal-title">{t}Confirm purchase{/t}</h4>
  </div>
  <div class="modal-body">
    {t}You are going to request the following modules. Are you sure?{/t}
    <div class="p-t-15" style="height: 400px;">
      <scrollable>
        <div class="m-b-15" ng-repeat="item in template.cart">
          <strong>[% item.name %]</strong>
        </div>
      </scrollable>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="close()">{t}Close{/t}</button>
    <button type="button" class="btn btn-primary" ng-click="confirm()">{t}Confirm{/t}</button>
  </div>
