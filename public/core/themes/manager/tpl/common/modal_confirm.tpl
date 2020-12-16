<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="dismiss();">&times;</button>
    <h4 class="modal-title">
        <i class="fa fa-trash-o"></i>
        <span ng-if="template.name == 'logout'">{t}Log out{/t}</span>
        <span ng-if="template.name == 'delete-instance'">{t}Delete instance{/t}</span>
        <span ng-if="template.name == 'delete-instances'">{t}Delete selected instances{/t}</span>
        <span ng-if="template.name == 'delete-user'">{t}Delete user{/t}</span>
        <span ng-if="template.name == 'delete-users'">{t}Delete selected users{/t}</span>
        <span ng-if="template.name == 'delete-user-group'">{t}Delete user group{/t}</span>
        <span ng-if="template.name == 'delete-user-groups'">{t}Delete selected user groups{/t}</span>
        <span ng-if="template.name == 'delete-notification'">{t}Delete notification{/t}</span>
        <span ng-if="template.name == 'delete-notifications'">{t}Delete notifications{/t}</span>
        <span ng-if="template.name == 'delete-module'">{t}Delete module{/t}</span>
        <span ng-if="template.name == 'delete-modules'">{t}Delete modules{/t}</span>
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
    <p ng-if="template.name == 'delete-notification'">{t}Do you really want to delete the notification?{/t}</p>
    <p ng-if="template.name == 'delete-notifications'">{t}Do you really want to delete the selected notifications?{/t}</p>
    <p ng-if="template.name == 'delete-module'">{t}Do you really want to delete the selected modules?{/t}</p>
    <p ng-if="template.name == 'delete-modules'">{t}Do you really want to delete the selected modules?{/t}</p>
    <ul ng-if="template.item">
        <li>
            [% template.name == 'delete-module' || template.name == 'delete-modules' ? template.item.name.en : template.item.name %]
            <span ng-if="template.item.domains">
                <a ng-href="http://[% template.item.domains[template.item.main_domain - 1] %]" ng-if="template.item.main_domain > 0">
                    ([% template.item.domains[template.item.main_domain - 1] %])
                </a>
                <a ng-href="http://[% template.item.domains[0] %]" ng-if="template.item.main_domain <= 0">
                    ([% template.item.domains[0] %])
                </a>
            </span>
        </li>
    </ul>
    <ul ng-if="template.selected">
      <li ng-repeat="item in template.selected">
            [% template.name == 'delete-module' || template.name == 'delete-modules' ? item.name.en : item.name %]
            <span ng-if="item.domains">
                <a ng-href="http://[% item.domains[item.main_domain - 1] %]" ng-if="item.main_domain > 0">
                    ([% item.domains[item.main_domain - 1] %])
                </a>
            </span>
            <a ng-href="http://[% item.domains[0] %]" ng-if="item.main_domain <= 0">
                ([% item.domains[0] %])
            </a>
        </li>
    </ul>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-link" data-dismiss="modal" ng-click="dismiss();" ng-disabled="loading">
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
        <span ng-if="template.name == 'delete-notification'">{t}delete it{/t}</span>
        <span ng-if="template.name == 'delete-notifications'">{t}delete them{/t}</span>
        <span ng-if="template.name == 'delete-module'">{t}delete it{/t}</span>
        <span ng-if="template.name == 'delete-modules'">{t}delete them{/t}</span>
    </button>
</div>
