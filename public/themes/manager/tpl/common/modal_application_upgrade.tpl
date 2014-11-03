<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
    <h4 class="modal-title">
        {t}Application upgraded{/t}
    </h4>
</div>
<div class="modal-body">
    {t}The application has been upgraded. Please reload your page{/t}
</div>
<div class="modal-footer text-center">
    <button class="btn btn-primary btn-lg" ng-click="reload();" ng-disabled="loading" type="button">
        <i class="fa fa-circle-o-notch fa-spin" ng-if="loading"></i>
        {t}Reload{/t}
    </button>
</div>
