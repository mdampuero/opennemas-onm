<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
    <h4 class="modal-title">
        <i class="fa fa-trash-o"></i> {t}Delete items{/t}
    </h4>
</div>
<div class="modal-body">
    {t}Do you want to delete the selected items?{/t}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal" ng-click="close();" ng-disabled="deleting">
        {t}Cancel{/t}
    </button>
    <button type="button" class="btn btn-primary" ng-click="delete();" ng-disabled="deleting" ng-if="!multiple">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': deleting }"></i>
        {t}Yes, delete it{/t}
    </button>
    <button type="button" class="btn btn-primary" ng-click="deleteSelected();" ng-disabled="deleting" ng-if="multiple">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': deleting }"></i>
        {t}Yes, delete all{/t}
    </button>
</div>
