<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ng-click="close()">&times;</button>
    <h4 class="modal-title">{t}Log in{/t}</h4>
</div>
<div class="modal-body">
    <form name="modalLoginForm">
        <div class="form-group">
            <label for="username">{t}Username or email{/t}</label>
            <input class="form-control" id="username" ng-model="user.username" required placeholder="Username or email" type="text">
        </div>
        <div class="form-group">
            <label for="password">{t}Password{/t}</label>
            <input class="form-control" id="password" ng-model="user.password" required type="password">
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" ng-click="login();" ng-disabled="loading">
        <i class="fa fa-circle-o-notch fa-spin" ng-if="loading"></i>
        Sign in
    </button>
</div>
