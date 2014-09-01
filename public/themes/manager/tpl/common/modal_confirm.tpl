<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
    <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        <span ng-if="template.name == 'logout'">{t}Log out{/t}</span>
    </h4>
</div>
<div class="modal-body">
    <p ng-if="template.name == 'logout'">{t}Do you really want to exit from backend?{/t}</p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal" ng-click="close();" ng-disabled="loading">
        {t}No{/t}
    </button>
    <button type="button" class="btn btn-primary" ng-click="confirm();" ng-disabled="loading">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
        {t}Yes{/t},
        <span ng-if="template.name == 'logout'">{t}log out{/t}</span>

    </button>
</div>
