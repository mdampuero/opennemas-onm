<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close();">&times;</button>
    <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        <span ng-if="template.name == 'logout'">{t}Log out{/t}</span>
        <span ng-if="template.name == 'delete-instance'">{t}Delete instance{/t}</span>
        <span ng-if="template.name == 'delete-instances'">{t}Delete selected instances{/t}</span>
        <span ng-if="template.name == 'delete-user'">{t}Delete user{/t}</span>
        <span ng-if="template.name == 'delete-users'">{t}Delete selected users{/t}</span>
        <span ng-if="template.name == 'delete-user-group'">{t}Delete user group{/t}</span>
        <span ng-if="template.name == 'delete-user-groups'">{t}Delete selected user groups{/t}</span>
    </h4>
</div>
<div class="modal-body">
    <p ng-if="template.name == 'logout'">{t}Do you really want to exit from backend?{/t}</p>
    <p ng-if="template.name == 'delete-instance'">{t}Do you really want to delete the instance?{/t}</p>
    <p ng-if="template.name == 'delete-instances'">{t}Do you really want to delete the selected instances?{/t}</p>
    <p ng-if="template.name == 'delete-user'">{t}Do you really want to delete the user?{/t}</p>
    <p ng-if="template.name == 'delete-users'">{t}Do you really want to delete the selected users?{/t}</p>
    <p ng-if="template.name == 'delete-user-group'">{t}Do you really want to delete the user group?{/t}</p>
    <p ng-if="template.name == 'delete-user-groups'">{t}Do you really want to delete the selected user groups?{/t}</p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal" ng-click="close();" ng-disabled="loading">
        {t}No{/t}
    </button>
    <button type="button" class="btn btn-primary" ng-click="confirm();" ng-disabled="loading">
        <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': loading }"></i>
        {t}Yes{/t},
        <span ng-if="template.name == 'logout'">{t}log out{/t}</span>
        <span ng-if="template.name == 'delete-instance'">{t}delete it{/t}</span>
        <span ng-if="template.name == 'delete-instances'">{t}delete them{/t}</span>
        <span ng-if="template.name == 'delete-user'">{t}delete it{/t}</span>
        <span ng-if="template.name == 'delete-users'">{t}delete them{/t}</span>
        <span ng-if="template.name == 'delete-user-group'">{t}delete it{/t}</span>
        <span ng-if="template.name == 'delete-user-groups'">{t}delete them{/t}</span>
    </button>
</div>
